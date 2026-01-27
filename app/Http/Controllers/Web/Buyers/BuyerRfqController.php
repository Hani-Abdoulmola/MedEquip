<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RfqRequest;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use App\Services\RfqImportService;
use App\Services\SupplierSuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Buyer RFQ Controller
 *
 * Handles RFQ management for buyers.
 * Buyers can create, view, edit, and delete their own RFQs.
 */
class BuyerRfqController extends Controller
{
    /**
     * Display list of buyer's RFQs.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Rfq::class);

        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $query = Rfq::with(['items', 'quotations', 'assignedSuppliers'])
            ->where('buyer_id', $buyer->id)
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $rfqs = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Rfq::where('buyer_id', $buyer->id)->count(),
            'open' => Rfq::where('buyer_id', $buyer->id)->where('status', 'open')->count(),
            'closed' => Rfq::where('buyer_id', $buyer->id)->where('status', 'closed')->count(),
            'awarded' => Rfq::where('buyer_id', $buyer->id)->where('status', 'awarded')->count(),
            'cancelled' => Rfq::where('buyer_id', $buyer->id)->where('status', 'cancelled')->count(),
            'total_quotations' => \App\Models\Quotation::whereHas('rfq', function($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            })->count(),
        ];

        return view('buyer.rfqs.index', compact('rfqs', 'stats'));
    }

    /**
     * Show the form for creating a new RFQ.
     */
    public function create(): View
    {
        $this->authorize('create', Rfq::class);

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('buyer.rfqs.create', compact('products'));
    }

    /**
     * Store a newly created RFQ.
     */
    public function store(RfqRequest $request): RedirectResponse
    {
        $this->authorize('create', Rfq::class);

        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        // Validate that RFQ has at least one item
        $items = $request->input('items', []);
        if (empty($items) || count($items) === 0) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'يجب إضافة على الأقل بند واحد إلى الطلب.']);
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['buyer_id'] = $buyer->id; // Auto-set buyer_id from authenticated user
            $data['created_by'] = Auth::id();
            
            // IMPORTANT: Fix is_public handling from checkbox
            // Checkbox sends '1' when checked, hidden input sends '0' when unchecked
            // Laravel's boolean validation converts '1'/'0' to true/false, but let's be explicit
            $isPublicInput = $request->input('is_public');
            if (is_string($isPublicInput)) {
                $data['is_public'] = ($isPublicInput === '1' || $isPublicInput === 'true' || $isPublicInput === 'on');
            } else {
                $data['is_public'] = (bool) $isPublicInput;
            }
            
            // If status is not explicitly set to 'open', determine based on is_public:
            // - If public, default to 'open' so suppliers can see it immediately
            // - If private, default to 'draft' (needs manual assignment/publishing)
            if (!isset($data['status']) || $data['status'] === 'draft') {
                $data['status'] = $data['is_public'] ? 'open' : 'draft';
            }
            
            // IMPORTANT: If user explicitly sets status to 'open' but is_public is false,
            // we should still allow it (maybe buyer wants to assign suppliers manually later)
            // But if is_public is true and status is 'open', it will be visible to all verified suppliers
            
            $data['reference_code'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_RFQ,
                Rfq::class
            );

            $rfq = Rfq::create($data);

            // Create RFQ items
            foreach ($items as $item) {
                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'specifications' => $item['specifications'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'وحدة',
                ]);
            }

            // Notify verified suppliers about new public RFQ using workflow service
            // Only notify if RFQ is public AND open (visible to suppliers)
            if ($rfq->is_public && $rfq->status === 'open') {
                \App\Services\RfqWorkflowService::notifyNewRfq($rfq);
            }

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                    'reference_code' => $rfq->reference_code,
                    'items_count' => count($items),
                ])
                ->log('قام المشتري بإنشاء RFQ جديد');

            DB::commit();

            return redirect()
                ->route('buyer.rfqs.show', $rfq)
                ->with('success', '✅ تم إنشاء طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer RFQ creation error', [
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    /**
     * Display RFQ details with quotations.
     */
    public function show(Rfq $rfq): View
    {
        $this->authorize('view', $rfq);

        $buyer = Auth::user()->buyerProfile;

        // Ensure RFQ belongs to buyer
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا الطلب');
        }

        $rfq->load([
            'items',
            'quotations.supplier.user',
            'quotations.items',
            'assignedSuppliers',
        ]);

        return view('buyer.rfqs.show', compact('rfq'));
    }

    /**
     * Show the form for editing the specified RFQ.
     */
    public function edit(Rfq $rfq): View|RedirectResponse
    {
        $this->authorize('update', $rfq);

        $buyer = Auth::user()->buyerProfile;

        // Ensure RFQ belongs to buyer
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الطلب');
        }

        // Check if RFQ can be edited
        if (!in_array($rfq->status, ['draft', 'open'])) {
            return redirect()
                ->route('buyer.rfqs.show', $rfq)
                ->with('error', 'لا يمكن تعديل الطلب - الحالة غير مناسبة');
        }

        // Check if RFQ has quotations
        if ($rfq->quotations()->count() > 0) {
            return redirect()
                ->route('buyer.rfqs.show', $rfq)
                ->with('error', 'لا يمكن تعديل الطلب - يوجد عروض أسعار مرفقة');
        }

        $rfq->load('items');
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('buyer.rfqs.edit', compact('rfq', 'products'));
    }

    /**
     * Update the specified RFQ.
     */
    public function update(RfqRequest $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('update', $rfq);

        $buyer = Auth::user()->buyerProfile;

        // Ensure RFQ belongs to buyer
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الطلب');
        }

        // Check if RFQ can be edited
        if (!in_array($rfq->status, ['draft', 'open'])) {
            return back()->withErrors(['error' => 'لا يمكن تعديل الطلب - الحالة غير مناسبة']);
        }

        // Check if RFQ has quotations
        if ($rfq->quotations()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن تعديل الطلب - يوجد عروض أسعار مرفقة']);
        }

        // Validate that RFQ has at least one item
        $items = $request->input('items', []);
        if (empty($items) || count($items) === 0) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'يجب إضافة على الأقل بند واحد إلى الطلب.']);
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();
            
            // IMPORTANT: Fix is_public handling from checkbox (same as store method)
            $isPublicInput = $request->input('is_public');
            if (is_string($isPublicInput)) {
                $data['is_public'] = ($isPublicInput === '1' || $isPublicInput === 'true' || $isPublicInput === 'on');
            } else {
                $data['is_public'] = (bool) $isPublicInput;
            }

            // Update RFQ
            $rfq->update($data);

            // Update or create RFQ items
            $existingItemIds = [];
            foreach ($items as $item) {
                if (isset($item['id']) && $item['id']) {
                    // Update existing item
                    $rfqItem = RfqItem::find($item['id']);
                    if ($rfqItem && $rfqItem->rfq_id === $rfq->id) {
                        $rfqItem->update([
                            'product_id' => $item['product_id'] ?? null,
                            'item_name' => $item['item_name'],
                            'specifications' => $item['specifications'] ?? null,
                            'quantity' => $item['quantity'],
                            'unit' => $item['unit'] ?? 'وحدة',
                        ]);
                        $existingItemIds[] = $rfqItem->id;
                    }
                } else {
                    // Create new item
                    $rfqItem = RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'product_id' => $item['product_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'specifications' => $item['specifications'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'] ?? 'وحدة',
                    ]);
                    $existingItemIds[] = $rfqItem->id;
                }
            }

            // Delete items that were removed
            $rfq->items()->whereNotIn('id', $existingItemIds)->delete();

            // Notify suppliers if RFQ status changed to open
            if ($rfq->status === 'open' && $rfq->is_public) {
                $suppliers = Supplier::where('is_verified', true)->get();
                foreach ($suppliers as $supplier) {
                    if ($supplier->user) {
                        NotificationService::send(
                            $supplier->user,
                            '🆕 طلب عرض سعر جديد',
                            "يوجد طلب عرض سعر جديد بعنوان: {$rfq->title}.",
                            route('supplier.rfqs.show', $rfq->id)
                        );
                    }
                }
            }

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'status' => $rfq->status,
                    'items_count' => count($items),
                ])
                ->log('قام المشتري بتحديث RFQ');

            DB::commit();

            return redirect()
                ->route('buyer.rfqs.show', $rfq)
                ->with('success', '✅ تم تحديث طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer RFQ update error', [
                'rfq_id' => $rfq->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'فشل التحديث: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified RFQ.
     */
    public function destroy(Rfq $rfq): RedirectResponse
    {
        $this->authorize('delete', $rfq);

        $buyer = Auth::user()->buyerProfile;

        // Ensure RFQ belongs to buyer
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الطلب');
        }

        // Check if RFQ has quotations
        if ($rfq->quotations()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف الطلب - يوجد عروض أسعار مرفقة']);
        }

        DB::beginTransaction();

        try {
            $rfqTitle = $rfq->title;
            $rfq->delete();

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'rfq_title' => $rfqTitle,
                    'rfq_reference_code' => $rfq->reference_code,
                ])
                ->log('قام المشتري بحذف RFQ');

            DB::commit();

            return redirect()
                ->route('buyer.rfqs.index')
                ->with('success', '❌ تم حذف طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer RFQ deletion error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل الحذف: ' . $e->getMessage()]);
        }
    }

    /**
     * Update RFQ status.
     */
    public function updateStatus(Request $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('updateStatus', $rfq);

        $buyer = Auth::user()->buyerProfile;

        // Ensure RFQ belongs to buyer
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لتغيير حالة هذا الطلب');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,open,closed,cancelled',
        ]);

            $oldStatus = $rfq->status;

        DB::beginTransaction();

        try {
            $rfq->update([
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
                'closed_at' => in_array($validated['status'], ['closed', 'cancelled']) ? now() : null,
            ]);

            // Notify suppliers when RFQ status changes to 'open' (becomes visible)
            if ($oldStatus !== 'open' && $rfq->status === 'open' && $rfq->is_public) {
                \App\Services\RfqWorkflowService::notifyNewRfq($rfq);
            }

            // Notify assigned suppliers if RFQ is closed
            if ($rfq->status === 'closed') {
                foreach ($rfq->assignedSuppliers as $supplier) {
                    if ($supplier->user) {
                        NotificationService::send(
                            $supplier->user,
                            '🚫 تم إغلاق RFQ',
                            "تم إغلاق الطلب: {$rfq->title}.",
                            route('supplier.rfqs.index')
                        );
                    }
                }
            }

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                ])
                ->log('قام المشتري بتغيير حالة RFQ');

            DB::commit();

            return back()->with('success', 'تم تحديث حالة الطلب بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer RFQ status update error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث الحالة']);
        }
    }

    /**
     * Duplicate an existing RFQ.
     */
    public function duplicate(Rfq $rfq): RedirectResponse
    {
        $this->authorize('create', Rfq::class);

        $buyer = Auth::user()->buyerProfile;

        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لتكرار هذا الطلب');
        }

        DB::beginTransaction();

        try {
            // Create new RFQ
            $newRfq = Rfq::create([
                'buyer_id' => $buyer->id,
                'created_by' => Auth::id(),
                'title' => 'نسخة من: ' . $rfq->title,
                'description' => $rfq->description,
                'deadline' => now()->addDays(7), // New deadline
                'is_public' => $rfq->is_public,
                'status' => 'draft',
                'reference_code' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_RFQ,
                    Rfq::class
                ),
            ]);

            // Copy items
            $rfq->load('items');
            foreach ($rfq->items as $item) {
                RfqItem::create([
                    'rfq_id' => $newRfq->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('buyer.rfqs.edit', $newRfq)
                ->with('success', 'تم تكرار الطلب بنجاح. يمكنك الآن مراجعة وتعديله.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RFQ duplication error', [
                'original_rfq_id' => $rfq->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تكرار الطلب']);
        }
    }

    /**
     * Import RFQ from CSV file.
     */
    public function importCsv(Request $request, RfqImportService $importService): RedirectResponse
    {
        $this->authorize('create', Rfq::class);

        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date|after:today',
            'is_public' => 'boolean',
        ]);

        $buyer = Auth::user()->buyerProfile;

        $results = $importService->importFromCsv(
            $request->file('csv_file'),
            $buyer,
            $validated
        );

        if ($results['success']) {
            $message = "تم استيراد {$results['imported']} منتج بنجاح";
            if ($results['skipped'] > 0) {
                $message .= " (تم تخطي {$results['skipped']} منتج)";
            }

            return redirect()
                ->route('buyer.rfqs.edit', $results['rfq'])
                ->with('success', $message);
        } else {
            return back()
                ->withInput()
                ->withErrors(['csv_file' => implode(', ', $results['errors'])]);
        }
    }

    /**
     * Download sample CSV template.
     */
    public function downloadCsvSample(RfqImportService $importService)
    {
        return $importService->downloadSampleCsv();
    }

    /**
     * Estimate budget for RFQ.
     */
    public function estimateBudget(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $minEstimate = 0;
        $maxEstimate = 0;
        $breakdown = [];
        $itemsWithPrices = 0;
        $totalItems = count($validated['items']);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);

            // Get price range from suppliers
            $prices = DB::table('product_supplier')
                ->where('product_id', $product->id)
                ->where('status', 'available')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('suppliers')
                        ->whereColumn('suppliers.id', 'product_supplier.supplier_id')
                        ->where('suppliers.is_verified', true)
                        ->where('suppliers.is_active', true);
                })
                ->pluck('price');

            if ($prices->isNotEmpty()) {
                $minPrice = $prices->min();
                $maxPrice = $prices->max();

                $itemMinTotal = $minPrice * $item['quantity'];
                $itemMaxTotal = $maxPrice * $item['quantity'];

                $minEstimate += $itemMinTotal;
                $maxEstimate += $itemMaxTotal;

                $breakdown[] = [
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'min_total' => $itemMinTotal,
                    'max_total' => $itemMaxTotal,
                ];

                $itemsWithPrices++;
            }
        }

        // Calculate confidence level
        $confidence = match (true) {
            $itemsWithPrices === $totalItems => 'high',
            $itemsWithPrices >= ($totalItems * 0.7) => 'medium',
            default => 'low',
        };

        return response()->json([
            'min_estimate' => round($minEstimate, 2),
            'max_estimate' => round($maxEstimate, 2),
            'avg_estimate' => round(($minEstimate + $maxEstimate) / 2, 2),
            'confidence' => $confidence,
            'items_with_prices' => $itemsWithPrices,
            'total_items' => $totalItems,
            'breakdown' => $breakdown,
            'message' => $confidence === 'high' 
                ? 'التقدير دقيق بناءً على الأسعار المتوفرة' 
                : 'التقدير تقريبي - بعض المنتجات ليس لها أسعار',
        ]);
    }

    /**
     * Suggest suppliers for RFQ.
     */
    public function suggestSuppliers(Request $request, SupplierSuggestionService $suggestionService): JsonResponse
    {
        $validated = $request->validate([
            'rfq_id' => 'required|exists:rfqs,id',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $rfq = Rfq::findOrFail($validated['rfq_id']);

        // Ensure RFQ belongs to buyer
        $buyer = Auth::user()->buyerProfile;
        if ($rfq->buyer_id !== $buyer->id) {
            abort(403);
        }

        $suggestions = $suggestionService->suggestForRfq(
            $rfq,
            $validated['limit'] ?? 10
        );

        $formatted = $suggestions->map(function ($suggestion) use ($suggestionService, $rfq) {
            $supplier = $suggestion['supplier'];
            return [
                'id' => $supplier->id,
                'company_name' => $supplier->company_name,
                'city' => $supplier->city,
                'score' => $suggestion['score'],
                'breakdown' => $suggestion['breakdown'],
                'reasons' => $suggestionService->getRecommendationReasons($supplier, $rfq),
            ];
        });

        return response()->json([
            'suggestions' => $formatted,
            'total' => $formatted->count(),
        ]);
    }

    /**
     * Suggest deadline for RFQ based on products.
     */
    public function suggestDeadline(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
        ]);

        $maxLeadTime = 0;

        foreach ($validated['items'] as $item) {
            // Get max lead time from all suppliers for this product
            $leadTime = DB::table('product_supplier')
                ->where('product_id', $item['product_id'])
                ->where('status', 'available')
                ->max('lead_time');

            if ($leadTime && $leadTime > $maxLeadTime) {
                $maxLeadTime = $leadTime;
            }
        }

        // Calculate suggested deadline
        // Formula: max_lead_time + 5 days buffer + 3 days for quotation preparation
        $minDays = max(7, $maxLeadTime + 3); // Minimum 7 days
        $recommendedDays = $maxLeadTime + 8; // Lead time + buffer + quotation time
        $suggestedDeadline = now()->addDays($recommendedDays);

        return response()->json([
            'suggested_deadline' => $suggestedDeadline->format('Y-m-d'),
            'min_days' => $minDays,
            'recommended_days' => $recommendedDays,
            'max_lead_time' => $maxLeadTime,
            'reasoning' => "بناءً على متوسط وقت التوصيل ({$maxLeadTime} أيام) + وقت إعداد العروض (3 أيام) + مهلة إضافية (5 أيام)",
        ]);
    }
}

