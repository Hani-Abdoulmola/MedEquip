<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Exports\SupplierQuotationsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\SupplierQuotationRequest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Supplier RFQ Controller
 *
 * Handles RFQ viewing and quotation submission for suppliers.
 * Suppliers can only see RFQs assigned to them or where they have existing quotations.
 */
class SupplierRfqController extends Controller
{
    /**
     * Display list of RFQs assigned to the supplier.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Rfq::class);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Rfq::with(['buyer', 'items', 'quotations' => function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        }])
        ->availableFor($supplier->id);

        // Filter by status
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Search
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if (request()->filled('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $rfqs = $query->latest()->paginate(15)->withQueryString();

        // Optimized stats calculation
        $rfqStats = Rfq::availableFor($supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as open
            ', ['open'])
            ->first();

        $quotationStats = Quotation::where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as quoted,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending
            ', ['pending'])
            ->first();

        $stats = [
            'total' => $rfqStats->total ?? 0,
            'open' => $rfqStats->open ?? 0,
            'quoted' => $quotationStats->quoted ?? 0,
            'pending' => $quotationStats->pending ?? 0,
        ];

        // Log activity
        activity('supplier_rfqs')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => request()->only(['status', 'search', 'date_from', 'date_to']),
            ])
            ->log('عرض المورد قائمة طلبات عروض الأسعار');

        return view('supplier.rfqs.index', compact('rfqs', 'stats'));
    }

    /**
     * Display RFQ details.
     */
    public function show(Rfq $rfq): View
    {
        $this->authorize('view', $rfq);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $rfq->load(['buyer', 'items', 'quotations' => function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id)->with('items');
        }]);

        // Mark as viewed
        $pivot = DB::table('rfq_supplier')
            ->where('rfq_id', $rfq->id)
            ->where('supplier_id', $supplier->id)
            ->first();

        $wasViewed = false;
        if ($pivot && !$pivot->viewed_at) {
            DB::table('rfq_supplier')
                ->where('rfq_id', $rfq->id)
                ->where('supplier_id', $supplier->id)
                ->update([
                    'viewed_at' => now(),
                    'status' => 'viewed',
                ]);
            $wasViewed = true;
        }

        // Log activity
        activity('supplier_rfqs')
            ->performedOn($rfq)
            ->causedBy(Auth::user())
            ->withProperties([
                'rfq_id' => $rfq->id,
                'rfq_title' => $rfq->title,
                'rfq_reference_code' => $rfq->reference_code,
                'was_first_view' => $wasViewed,
            ])
            ->log('عرض المورد تفاصيل طلب عرض أسعار: ' . $rfq->reference_code);

        $myQuotation = $rfq->quotations->first();

        return view('supplier.rfqs.show', compact('rfq', 'myQuotation'));
    }

    /**
     * Show form to create a quotation for an RFQ.
     */
    public function createQuote(Rfq $rfq): View|RedirectResponse
    {
        $this->authorize('createQuotation', $rfq);

        $supplier = Auth::user()->supplierProfile;

        // Check if already quoted
        if ($rfq->hasQuotationFrom($supplier->id)) {
            return redirect()
                ->route('supplier.quotations.edit', $rfq->quotations()->where('supplier_id', $supplier->id)->first())
                ->with('info', 'لديك عرض سعر موجود بالفعل. يمكنك تعديله.');
        }

        // Check if RFQ is still open
        if ($rfq->status !== 'open') {
            return redirect()
                ->route('supplier.rfqs.show', $rfq)
                ->with('error', 'هذا الطلب مغلق ولا يمكن تقديم عروض عليه.');
        }

        // Check if RFQ deadline has passed
        if ($rfq->deadline && $rfq->deadline->isPast()) {
            return redirect()
                ->route('supplier.rfqs.show', $rfq)
                ->with('error', 'انتهت فترة تقديم العروض لهذا الطلب.');
        }

        $rfq->load(['buyer', 'items']);

        return view('supplier.rfqs.quote', compact('rfq'));
    }

    /**
     * Store a new quotation.
     */
    public function storeQuote(SupplierQuotationRequest $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('createQuotation', $rfq);

        $supplier = Auth::user()->supplierProfile;

        // Check if already quoted
        if ($rfq->hasQuotationFrom($supplier->id)) {
            return redirect()
                ->route('supplier.rfqs.show', $rfq)
                ->with('error', 'لديك عرض سعر موجود بالفعل.');
        }

        // Validate RFQ can accept quotations using workflow service
        $validation = \App\Services\RfqWorkflowService::canAcceptQuotations($rfq);
        if (!$validation['valid']) {
            return redirect()
                ->route('supplier.rfqs.show', $rfq)
                ->with('error', $validation['message']);
        }

        DB::beginTransaction();

        try {
            // Re-check RFQ status inside transaction (race condition prevention)
            $rfq->refresh();
            if ($rfq->status !== 'open') {
                DB::rollBack();
                return redirect()
                    ->route('supplier.rfqs.show', $rfq)
                    ->with('error', 'الطلب تم إغلاقه أثناء تقديم العرض.');
            }

            // Re-check deadline inside transaction
            if ($rfq->deadline && $rfq->deadline->isPast()) {
                DB::rollBack();
                return redirect()
                    ->route('supplier.rfqs.show', $rfq)
                    ->with('error', 'انتهت فترة تقديم العروض لهذا الطلب.');
            }
            // Calculate total from items if provided
            $items = $request->input('items', []);
            $totalPrice = $this->calculateQuotationTotal($request, $rfq);

            $quotation = Quotation::create([
                'rfq_id' => $rfq->id,
                'supplier_id' => $supplier->id,
                'reference_code' => ReferenceCodeService::generateUnique('QUO', Quotation::class),
                'total_price' => $totalPrice,
                'terms' => $request->terms,
                'status' => 'pending',
                'valid_until' => $request->valid_until,
            ]);

            // Create quotation items
            if (!empty($items)) {
                $this->createQuotationItems($quotation, $items, $rfq);
            }

            // Upload attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $quotation->addMedia($file)->toMediaCollection('quotation_documents');
                }
            }

            // Update rfq_supplier status
            DB::table('rfq_supplier')
                ->where('rfq_id', $rfq->id)
                ->where('supplier_id', $supplier->id)
                ->update(['status' => 'quoted']);

            // Notify admin
            NotificationService::notifyAdmins(
                '📋 عرض سعر جديد',
                "تم تقديم عرض سعر جديد من المورد {$supplier->company_name} للطلب: {$rfq->title}",
                route('admin.quotations.show', $quotation->id)
            );

            // Notify buyer using workflow service
            \App\Services\RfqWorkflowService::notifyQuotationSubmitted($quotation);

            // Log activity
            activity('supplier_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'total_price' => $quotation->total_price,
                    'items_count' => count($items),
                ])
                ->log('قدم المورد عرض سعر جديد');

            DB::commit();

            return redirect()
                ->route('supplier.rfqs.show', $rfq)
                ->with('success', 'تم تقديم عرض السعر بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quotation creation error', [
                'supplier_id' => $supplier->id,
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تقديم العرض. يرجى التحقق من البيانات والمحاولة مرة أخرى.']);
        }
    }

    /**
     * Show form to edit an existing quotation.
     */
    public function editQuote(Quotation $quotation): View
    {
        $this->authorize('update', $quotation);

        $supplier = Auth::user()->supplierProfile;

        $quotation->load(['rfq.buyer', 'rfq.items', 'items']);

        // Log activity
        activity('supplier_quotations')
            ->performedOn($quotation)
            ->causedBy(Auth::user())
            ->withProperties([
                'quotation_id' => $quotation->id,
                'quotation_reference_code' => $quotation->reference_code,
                'rfq_id' => $quotation->rfq_id,
            ])
            ->log('فتح المورد صفحة تعديل عرض السعر: ' . $quotation->reference_code);

        return view('supplier.rfqs.quote-edit', compact('quotation'));
    }

    /**
     * Update an existing quotation.
     */
    public function updateQuote(SupplierQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        $supplier = Auth::user()->supplierProfile;

        // Check if RFQ deadline has passed
        if ($quotation->rfq->deadline && $quotation->rfq->deadline->isPast()) {
            return redirect()
                ->route('supplier.rfqs.show', $quotation->rfq)
                ->with('error', 'انتهت فترة تقديم العروض. لا يمكن تعديل العرض.');
        }

        DB::beginTransaction();

        try {
            // Re-check RFQ status inside transaction
            $quotation->rfq->refresh();
            if ($quotation->rfq->status !== 'open') {
                DB::rollBack();
                return redirect()
                    ->route('supplier.rfqs.show', $quotation->rfq)
                    ->with('error', 'الطلب لم يعد مفتوحاً. لا يمكن تعديل العرض.');
            }

            // Calculate total from items if provided
            $items = $request->input('items', []);
            $totalPrice = $this->calculateQuotationTotal($request, $quotation->rfq);

            $quotation->update([
                'total_price' => $totalPrice,
                'terms' => $request->terms,
                'valid_until' => $request->valid_until,
                'status' => 'pending', // Reset to pending on update
            ]);

            // Update quotation items
            if (!empty($items)) {
                // Delete existing items
                $quotation->items()->delete();

                // Create new items
                $this->createQuotationItems($quotation, $items, $quotation->rfq);
            }

            // Upload new attachments if provided
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $quotation->addMedia($file)->toMediaCollection('quotation_documents');
                }
            }

            // Notify admin
            NotificationService::notifyAdmins(
                '✏ تحديث عرض سعر',
                "قام المورد {$supplier->company_name} بتحديث عرض السعر: {$quotation->reference_code} للطلب: {$quotation->rfq->title}",
                route('admin.quotations.show', $quotation->id)
            );

            // Notify buyer
            if ($quotation->rfq->buyer && $quotation->rfq->buyer->user) {
                NotificationService::send(
                    $quotation->rfq->buyer->user,
                    '✏ تم تحديث عرض سعر',
                    "قام المورد {$supplier->company_name} بتحديث عرض السعر لطلبك: {$quotation->rfq->title}",
                    route('admin.quotations.show', $quotation->id)
                );
            }

            // Log activity
            activity('supplier_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'quotation_id' => $quotation->id,
                    'quotation_reference_code' => $quotation->reference_code,
                    'rfq_id' => $quotation->rfq_id,
                    'total_price' => $quotation->total_price,
                    'items_count' => count($items),
                ])
                ->log('حدّث المورد عرض السعر: ' . $quotation->reference_code);

            DB::commit();

            return redirect()
                ->route('supplier.rfqs.show', $quotation->rfq)
                ->with('success', 'تم تحديث عرض السعر بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quotation update error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث العرض. يرجى التحقق من البيانات والمحاولة مرة أخرى.']);
        }
    }

    /**
     * Delete a pending quotation.
     */
    public function destroyQuote(Quotation $quotation): RedirectResponse
    {
        $this->authorize('delete', $quotation);

        $supplier = Auth::user()->supplierProfile;

        // Only pending quotations can be deleted
        if ($quotation->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف عرض غير قيد المراجعة');
        }

        $rfq = $quotation->rfq;

        DB::beginTransaction();

        try {
            // Delete quotation items first
            $quotation->items()->delete();

            // Delete quotation
            $quotation->delete();

            // Update rfq_supplier status back to viewed
            DB::table('rfq_supplier')
                ->where('rfq_id', $rfq->id)
                ->where('supplier_id', $supplier->id)
                ->update(['status' => 'viewed']);

            // Log activity
            activity('supplier_quotations')
                ->causedBy(Auth::user())
                ->withProperties([
                    'quotation_id' => $quotation->id,
                    'quotation_reference_code' => $quotation->reference_code,
                    'rfq_id' => $rfq->id,
                    'rfq_title' => $rfq->title,
                ])
                ->log('حذف المورد عرض سعر: ' . $quotation->reference_code);

            DB::commit();

            return redirect()
                ->route('supplier.quotations.index')
                ->with('success', 'تم حذف عرض السعر بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quotation deletion error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف العرض. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Display supplier's quotations.
     */
    public function myQuotations(): View
    {
        $this->authorize('viewAny', Quotation::class);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Quotation::with(['rfq.buyer'])
            ->where('supplier_id', $supplier->id);

        // Filter by status (supports multiple statuses)
        if (request()->filled('status')) {
            $statuses = is_array(request('status')) ? request('status') : [request('status')];
            $query->whereIn('status', $statuses);
        }

        // Enhanced search across multiple fields
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhere('terms', 'like', "%{$search}%")
                  ->orWhereHas('rfq', function ($sub) use ($search) {
                      $sub->where('title', 'like', "%{$search}%")
                          ->orWhere('reference_code', 'like', "%{$search}%")
                          ->orWhereHas('buyer', fn($buyer) => $buyer->where('organization_name', 'like', "%{$search}%"));
                  });
            });
        }

        // Date range filter with quick filters
        if (request()->filled('date_filter')) {
            $dateFilter = request('date_filter');
            match ($dateFilter) {
                'today' => $query->whereDate('created_at', today()),
                'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'last_month' => $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year),
                default => null,
            };
        } else {
            // Custom date range
            if (request()->filled('date_from')) {
                $query->whereDate('created_at', '>=', request('date_from'));
            }
            if (request()->filled('date_to')) {
                $query->whereDate('created_at', '<=', request('date_to'));
            }
        }

        // Price range filter
        if (request()->filled('price_min')) {
            $query->where('total_price', '>=', request('price_min'));
        }
        if (request()->filled('price_max')) {
            $query->where('total_price', '<=', request('price_max'));
        }

        $quotations = $query->latest()->paginate(15)->withQueryString();

        // Optimized stats calculation
        $stats = Quotation::where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
            ', [
                'pending',
                'accepted',
                'rejected',
            ])
            ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'accepted' => $stats->accepted ?? 0,
            'rejected' => $stats->rejected ?? 0,
        ];

        // Log activity
        activity('supplier_quotations')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => request()->only(['status', 'search', 'date_from', 'date_to']),
            ])
            ->log('عرض المورد قائمة عروض الأسعار المقدمة');

        return view('supplier.quotations.index', compact('quotations', 'stats'));
    }

    /**
     * Export quotations to Excel.
     */
    public function exportQuotations(Request $request): BinaryFileResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $filters = $request->only(['status', 'from_date', 'to_date']);

        // Log activity
        activity('supplier_quotations')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'action' => 'export',
                'filters' => $filters,
            ])
            ->log('قام المورد بتصدير قائمة عروض الأسعار');

        $fileName = 'quotations-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new SupplierQuotationsExport($supplier->id, $filters), $fileName);
    }

    /**
     * Display quotation details.
     */
    public function showQuotation(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $quotation->load([
            'rfq.buyer.user',
            'rfq.items',
            'supplier.user',
            'items.rfqItem',
        ]);

        // Log activity
        activity('supplier_quotations')
            ->performedOn($quotation)
            ->causedBy(Auth::user())
            ->withProperties([
                'quotation_id' => $quotation->id,
                'quotation_reference_code' => $quotation->reference_code,
                'rfq_id' => $quotation->rfq_id,
                'status' => $quotation->status,
                'total_price' => $quotation->total_price,
            ])
            ->log('عرض المورد تفاصيل عرض السعر: ' . $quotation->reference_code);

        return view('supplier.quotations.show', compact('quotation'));
    }

    /**
     * Calculate quotation total from items or use provided total.
     */
    private function calculateQuotationTotal($request, Rfq $rfq): float
    {
        $totalPrice = $request->total_price;
        $items = $request->input('items', []);

        if (!empty($items)) {
            $calculatedTotal = 0;
            foreach ($items as $item) {
                $rfqItem = RfqItem::find($item['rfq_item_id']);
                if ($rfqItem && !empty($item['unit_price'])) {
                    $calculatedTotal += floatval($item['unit_price']) * $rfqItem->quantity;
                }
            }
            if ($calculatedTotal > 0) {
                $totalPrice = $calculatedTotal;
            }
        }

        return $totalPrice;
    }

    /**
     * Create quotation items from request data.
     */
    private function createQuotationItems(Quotation $quotation, array $items, Rfq $rfq): void
    {
        foreach ($items as $item) {
            $rfqItem = RfqItem::find($item['rfq_item_id']);
            if ($rfqItem && !empty($item['unit_price'])) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'rfq_item_id' => $rfqItem->id,
                    'product_id' => $rfqItem->product_id,
                    'item_name' => $rfqItem->item_name ?? $rfqItem->product?->name ?? 'منتج',
                    'specifications' => $rfqItem->specifications,
                    'quantity' => $rfqItem->quantity,
                    'unit' => $rfqItem->unit ?? 'وحدة',
                    'unit_price' => $item['unit_price'],
                    'total_price' => floatval($item['unit_price']) * $rfqItem->quantity,
                    'lead_time' => $item['lead_time'] ?? null,
                    'warranty' => $item['warranty'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }
    }
}

