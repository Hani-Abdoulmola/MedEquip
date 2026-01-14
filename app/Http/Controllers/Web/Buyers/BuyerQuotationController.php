<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Buyer Quotation Controller
 *
 * Handles quotation evaluation for buyers.
 * Buyers can view, compare, accept, and reject quotations for their RFQs.
 */
class BuyerQuotationController extends Controller
{
    /**
     * Display list of quotations for buyer's RFQs.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quotation::class);

        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $query = Quotation::with(['rfq', 'supplier.user', 'items'])
            ->whereHas('rfq', function($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            })
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            'total' => Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', $buyer->id))->count(),
            'pending' => Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', $buyer->id))
                ->where('status', 'pending')->count(),
            'accepted' => Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', $buyer->id))
                ->where('status', 'accepted')->count(),
            'rejected' => Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', $buyer->id))
                ->where('status', 'rejected')->count(),
        ];

        // For filters
        $rfqs = Rfq::where('buyer_id', $buyer->id)
            ->orderBy('title')
            ->pluck('title', 'id');

        return view('buyer.quotations.index', compact('quotations', 'stats', 'rfqs'));
    }

    /**
     * Display quotation details.
     */
    public function show(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $buyer = Auth::user()->buyerProfile;

        // Ensure quotation belongs to buyer's RFQ
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا العرض');
        }

        $quotation->load([
            'rfq.items',
            'supplier.user',
            'items.rfqItem',
        ]);

        return view('buyer.quotations.show', compact('quotation'));
    }

    /**
     * Compare multiple quotations for an RFQ.
     */
    public function compare(Request $request): View
    {
        $this->authorize('compare', Quotation::class);

        $validated = $request->validate([
            'rfq_id' => 'required|exists:rfqs,id',
            'sort_by' => 'nullable|in:price_asc,price_desc,date_asc,date_desc,supplier',
            'filter_status' => 'nullable|in:pending,accepted,rejected',
        ]);

        $buyer = Auth::user()->buyerProfile;

        $rfq = Rfq::with(['items', 'quotations.supplier.user', 'quotations.items.rfqItem'])
            ->where('buyer_id', $buyer->id)
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

        return view('buyer.quotations.compare', compact('rfq', 'stats'));
    }

    /**
     * Accept a quotation.
     */
    public function accept(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('accept', $quotation);

        $buyer = Auth::user()->buyerProfile;

        // Ensure quotation belongs to buyer's RFQ
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لقبول هذا العرض');
        }

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن قبول هذا العرض - الحالة غير مناسبة']);
        }

        DB::beginTransaction();

        try {
            $quotation->update([
                'status' => 'accepted',
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

            // Update RFQ status to awarded
            $quotation->rfq->update([
                'status' => 'awarded',
                'closed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Create Order automatically from accepted quotation
            $order = $this->createOrderFromQuotation($quotation, $buyer);

            // Notify supplier
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '🎉 تم قبول عرض السعر الخاص بك!',
                    "تم قبول عرضك للطلب: {$quotation->rfq->title}",
                    route('supplier.quotations.show', $quotation->id),
                    'fas fa-check-circle',
                    'success'
                );
            }

            // Notify rejected suppliers
            $rejectedQuotations = Quotation::where('rfq_id', $quotation->rfq_id)
                ->where('id', '!=', $quotation->id)
                ->where('status', 'rejected')
                ->with('supplier.user')
                ->get();

            foreach ($rejectedQuotations as $rejected) {
                if ($rejected->supplier && $rejected->supplier->user) {
                    NotificationService::send(
                        $rejected->supplier->user,
                        '❌ لم يتم قبول عرض السعر',
                        "للأسف، لم يتم قبول عرضك للطلب: {$quotation->rfq->title}. تم ترسية الطلب لمورد آخر.",
                        route('supplier.quotations.show', $rejected->id),
                        'fas fa-times-circle',
                        'warning'
                    );
                }
            }

            // Log activity
            activity('buyer_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'accept',
                    'rfq_id' => $quotation->rfq_id,
                    'supplier_id' => $quotation->supplier_id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ])
                ->log('قام المشتري بقبول عرض السعر');

            DB::commit();

            return redirect()
                ->route('buyer.orders.show', $order)
                ->with('success', "تم قبول عرض السعر وإنشاء الطلب رقم {$order->order_number} بنجاح");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer accept quotation error', [
                'quotation_id' => $quotation->id,
                'buyer_id' => $buyer->id,
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

        $buyer = Auth::user()->buyerProfile;

        // Ensure quotation belongs to buyer's RFQ
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لرفض هذا العرض');
        }

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
                    route('supplier.quotations.show', $quotation->id),
                    'fas fa-times-circle',
                    'warning'
                );
            }

            // Log activity
            activity('buyer_quotations')
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'reject',
                    'reason' => $validated['rejection_reason'] ?? null,
                ])
                ->log('قام المشتري برفض عرض السعر');

            DB::commit();

            return back()->with('success', 'تم رفض عرض السعر');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer reject quotation error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء رفض العرض']);
        }
    }

    /**
     * Create an Order from accepted Quotation.
     */
    private function createOrderFromQuotation(Quotation $quotation, $buyer): Order
    {
        // Load quotation items if not loaded
        $quotation->load('items');

        // Create the order
        $order = Order::create([
            'quotation_id' => $quotation->id,
            'buyer_id' => $buyer->id,
            'supplier_id' => $quotation->supplier_id,
            'order_number' => ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_ORDER,
                Order::class,
                'order_number'
            ),
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => $quotation->total_price,
            'currency' => 'LYD',
            'notes' => "تم إنشاء هذا الطلب تلقائياً من عرض السعر: {$quotation->reference_code}",
            'created_by' => Auth::id(),
        ]);

        // Create order items from quotation items
        foreach ($quotation->items as $quotationItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'quotation_item_id' => $quotationItem->id,
                'product_id' => $quotationItem->product_id,
                'item_name' => $quotationItem->item_name,
                'specifications' => $quotationItem->specifications,
                'quantity' => $quotationItem->quantity,
                'unit' => $quotationItem->unit,
                'unit_price' => $quotationItem->unit_price,
                'subtotal' => $quotationItem->total_price,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_price' => $quotationItem->total_price,
                'lead_time' => $quotationItem->lead_time,
                'warranty' => $quotationItem->warranty,
                'status' => 'pending',
                'notes' => $quotationItem->notes,
            ]);
        }

        // Notify supplier about new order
        if ($quotation->supplier && $quotation->supplier->user) {
            NotificationService::send(
                $quotation->supplier->user,
                '📦 طلب شراء جديد!',
                "تم إنشاء طلب شراء جديد رقم {$order->order_number} من المشتري {$buyer->organization_name}",
                route('supplier.orders.show', $order->id),
                'fas fa-shopping-cart',
                'success'
            );
        }

        // Notify admins about new order
        NotificationService::notifyAdmins(
            '📦 طلب شراء جديد',
            "تم إنشاء طلب شراء جديد رقم {$order->order_number}",
            route('admin.orders.show', $order->id)
        );

        // Log activity
        activity('buyer_orders')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'quotation_id' => $quotation->id,
                'total_amount' => $order->total_amount,
            ])
            ->log('تم إنشاء طلب شراء جديد من عرض السعر المقبول');

        return $order;
    }
}

