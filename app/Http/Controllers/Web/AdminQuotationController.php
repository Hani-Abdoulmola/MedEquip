<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use App\Exports\AdminQuotationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin Quotation Controller
 *
 * Handles complete quotation management for administrators.
 * Admin can create, view, edit, delete quotations, monitor their status, and manage approvals.
 */
class AdminQuotationController extends Controller
{
    /**
     * Display list of all quotations with filtering.
     */
    public function index(Request $request): View
    {
        // Permission check is handled by route middleware
        $query = Quotation::with(['rfq.buyer', 'supplier', 'items'])
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by RFQ
        if ($request->filled('rfq_id')) {
            $query->where('rfq_id', $request->rfq_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhereHas('rfq', fn ($qq) => $qq->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn ($qq) => $qq->where('company_name', 'like', "%{$search}%"));
            });
        }

        $quotations = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'accepted' => Quotation::where('status', 'accepted')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'total_value' => Quotation::where('status', 'accepted')->sum('total_price'),
        ];

        // For filters
        $suppliers = Supplier::orderBy('company_name')->pluck('company_name', 'id');
        $rfqs = Rfq::orderBy('title')->pluck('title', 'id');

        return view('admin.quotations.index', compact('quotations', 'stats', 'suppliers', 'rfqs'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create(): View
    {
        // Permission check is handled by route middleware

        // Get RFQs for dropdown
        $rfqs = Rfq::orderBy('title')->pluck('title', 'id');
        
        // Get Suppliers for dropdown
        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->orderBy('company_name')
            ->pluck('company_name', 'id');

        return view('admin.quotations.create', compact('rfqs', 'suppliers'));
    }

    /**
     * Store a newly created quotation.
     */
    public function store(QuotationRequest $request): RedirectResponse
    {
        // Permission check is handled by route middleware

        DB::beginTransaction();
        
        try {
            // Get RFQ to validate
            $rfq = Rfq::findOrFail($request->rfq_id);
            
            // Get Supplier to validate
            $supplier = Supplier::findOrFail($request->supplier_id);
            
            // Generate reference code
            $referenceCode = ReferenceCodeService::generate('QUO');
            
            // Create quotation
            $quotation = Quotation::create([
                'rfq_id' => $rfq->id,
                'supplier_id' => $supplier->id,
                'created_by' => Auth::id(),
                'reference_code' => $referenceCode,
                'total_price' => $request->total_price,
                'status' => $request->status ?? 'pending',
                'valid_until' => $request->valid_until ? now()->parse($request->valid_until) : null,
                'terms' => $request->terms,
                'created_by' => Auth::id(),
            ]);

            // Log activity
            activity()
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'total_price' => $quotation->total_price,
                    'status' => $quotation->status,
                ])
                ->log('تم إنشاء عرض سعر جديد من قبل المسؤول');

            DB::commit();

            return redirect()
                ->route('admin.quotations.show', $quotation)
                ->with('success', 'تم إنشاء عرض السعر بنجاح.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin quotation creation error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage()]);
        }
    }

    /**
     * Display quotation details.
     */
    public function show(Quotation $quotation): View
    {
        $quotation->load([
            'rfq.buyer.user',
            'rfq.items',
            'supplier.user',
            'items.rfqItem',
        ]);

        return view('admin.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified quotation.
     * 
     * CRITICAL FIX: Admin must NOT edit quotations directly.
     * Quotations must be managed by suppliers to maintain business logic integrity.
     */
    public function edit(Quotation $quotation): View
    {
        abort(403, 'لا يمكن للمسؤولين تعديل عروض الأسعار مباشرة.');
    }

    /**
     * Update the specified quotation.
     * 
     * CRITICAL FIX: Admin must NOT edit quotations directly.
     * Quotations must be managed by suppliers to maintain business logic integrity.
     */
    public function update(QuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        abort(403, 'لا يمكن للمسؤولين تعديل عروض الأسعار مباشرة.');
    }

    /**
     * Remove the specified quotation.
     */
    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize('delete', $quotation);

        try {
            $quotationTitle = $quotation->reference_code;
            $quotation->delete();

            // Notify supplier about deletion
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '⚠️ تم حذف عرض السعر',
                    "تم حذف عرض السعر رقم {$quotationTitle}",
                    route('supplier.quotations.index')
                );
            }

            // Log activity
            activity('admin_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties(['quotation_code' => $quotationTitle])
                ->log('قام المسؤول بحذف عرض السعر');

            return redirect()
                ->route('admin.quotations.index')
                ->with('success', '❌ تم حذف عرض السعر بنجاح');

        } catch (\Throwable $e) {
            Log::error('Admin quotation deletion error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل حذف عرض السعر: ' . $e->getMessage()]);
        }
    }

    /**
     * Accept a quotation.
     */
    public function accept(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('accept', $quotation);

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن قبول هذا العرض - الحالة غير مناسبة']);
        }

        DB::beginTransaction();

        try {
            $quotation->update([
                'status' => 'accepted',
                'updated_by' => Auth::id(),
            ]);

            // ALWAYS update RFQ status to awarded and reject others
            // Admin should not interfere with business logic, but if they accept, follow same rules
            if ($quotation->rfq) {
                $quotation->rfq->update([
                    'status' => 'awarded',
                    'closed_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

                // ALWAYS reject other quotations for this RFQ
                Quotation::where('rfq_id', $quotation->rfq_id)
                    ->where('id', '!=', $quotation->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'rejection_reason' => 'تم ترسية الطلب لمورد آخر',
                        'updated_by' => Auth::id(),
                    ]);
            }

            // Notify supplier
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '🎉 تم قبول عرض السعر الخاص بك!',
                    "تم قبول عرضك للطلب: {$quotation->rfq->title}",
                    route('supplier.quotations.index'),
                    'fas fa-check-circle',
                    'success'
                );
            }

            // Notify buyer
            if ($quotation->rfq && $quotation->rfq->buyer && $quotation->rfq->buyer->user) {
                NotificationService::send(
                    $quotation->rfq->buyer->user,
                    '✅ تم قبول عرض سعر لطلبك',
                    "تم قبول عرض من المورد {$quotation->supplier->company_name} للطلب: {$quotation->rfq->title}",
                    route('admin.rfqs.show', $quotation->rfq),
                    'fas fa-check',
                    'info'
                );
            }

            // Log activity
            activity('admin_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'accept',
                    'rfq_id' => $quotation->rfq_id,
                ])
                ->log('قام المسؤول بقبول عرض السعر');

            DB::commit();

            return back()->with('success', 'تم قبول عرض السعر بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin accept quotation error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء قبول العرض']);
        }
    }

    /**
     * Reject a quotation.
     */
    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('reject', $quotation);

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن رفض هذا العرض - الحالة غير مناسبة']);
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $quotation->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'] ?? 'لم يستوف المعايير المطلوبة',
                'updated_by' => Auth::id(),
            ]);

            // Notify supplier
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '❌ لم يتم قبول عرض السعر',
                    "للأسف، لم يتم قبول عرضك للطلب: {$quotation->rfq->title}. " . ($validated['rejection_reason'] ?? ''),
                    route('supplier.quotations.index'),
                    'fas fa-times-circle',
                    'warning'
                );
            }

            // Log activity
            activity('admin_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'reject',
                    'reason' => $validated['rejection_reason'] ?? null,
                ])
                ->log('قام المسؤول برفض عرض السعر');

            DB::commit();

            return back()->with('success', 'تم رفض عرض السعر');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin reject quotation error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء رفض العرض']);
        }
    }

    /**
     * Compare multiple quotations for an RFQ.
     */
    public function compare(Request $request): View
    {
        $validated = $request->validate([
            'rfq_id' => 'required|exists:rfqs,id',
            'sort_by' => 'nullable|in:price_asc,price_desc,date_asc,date_desc,supplier',
            'filter_status' => 'nullable|in:pending,accepted,rejected',
        ]);

        $rfq = Rfq::with(['items', 'quotations.supplier', 'quotations.items.rfqItem'])
            ->findOrFail($validated['rfq_id']);

        // Filter quotations by status if requested
        $quotations = $rfq->quotations;
        if ($request->filled('filter_status')) {
            $quotations = $quotations->where('status', $request->filter_status);
        }

        // Sort quotations
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $quotations = $quotations->sortBy('total_price');
                    break;
                case 'price_desc':
                    $quotations = $quotations->sortByDesc('total_price');
                    break;
                case 'date_asc':
                    $quotations = $quotations->sortBy('created_at');
                    break;
                case 'date_desc':
                    $quotations = $quotations->sortByDesc('created_at');
                    break;
                case 'supplier':
                    $quotations = $quotations->sortBy(function ($q) {
                        return $q->supplier->company_name ?? '';
                    });
                    break;
            }
        } else {
            // Default: sort by price ascending
            $quotations = $quotations->sortBy('total_price');
        }

        // Calculate comparison statistics
        $stats = [
            'total_quotations' => $quotations->count(),
            'min_price' => $quotations->min('total_price'),
            'max_price' => $quotations->max('total_price'),
            'avg_price' => $quotations->avg('total_price'),
            'price_range' => $quotations->max('total_price') - $quotations->min('total_price'),
        ];

        // Replace quotations collection with sorted/filtered one
        $rfq->setRelation('quotations', $quotations);

        return view('admin.quotations.compare', compact('rfq', 'stats'));
    }

    /**
     * 📥 تصدير عروض الأسعار إلى Excel
     */
    public function export()
    {
        $filters = request()->only(['search', 'status', 'supplier_id', 'rfq_id', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminQuotationsExport($filters),
            'quotations_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}

