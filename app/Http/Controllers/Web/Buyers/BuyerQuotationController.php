<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Services\NotificationService;
use App\Services\QuotationWorkflowService;
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

        // Add scores to quotations for pending ones
        $quotations->getCollection()->transform(function ($quotation) {
            if ($quotation->status === 'pending') {
                $quotation->score = $quotation->calculateScore();
                $quotation->score_breakdown = $quotation->getScoreBreakdown();
            }
            return $quotation;
        });

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

        // For filters - get full Rfq objects (view needs id, reference_code, and title)
        $rfqs = Rfq::where('buyer_id', $buyer->id)
            ->orderBy('title')
            ->get(['id', 'reference_code', 'title']);

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

        // Calculate score and breakdown for this quotation
        $quotation->score = $quotation->calculateScore();
        $quotation->score_breakdown = $quotation->getScoreBreakdown();
        $quotation->is_best_value = $quotation->isBestValue();

        // Get other quotations for comparison context
        $otherQuotations = Quotation::where('rfq_id', $quotation->rfq_id)
            ->where('id', '!=', $quotation->id)
            ->where('status', 'pending')
            ->with(['supplier'])
            ->get()
            ->map(function ($q) {
                $q->score = $q->calculateScore();
                return $q;
            });

        return view('buyer.quotations.show', compact('quotation', 'otherQuotations'));
    }

    /**
     * Compare multiple quotations for an RFQ.
     */
    public function compare(Request $request): View
    {
        $this->authorize('compare', Quotation::class);

        $validated = $request->validate([
            'rfq_id' => 'required|exists:rfqs,id',
            'sort_by' => 'nullable|in:price_asc,price_desc,date_asc,date_desc,supplier,score',
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

        // Calculate scores for all quotations
        $quotationsArray = $quotations->toArray();
        $scoredQuotations = $quotations->map(function ($quotation) use ($quotationsArray) {
            $quotation->score = $quotation->calculateScore($quotationsArray);
            $quotation->score_breakdown = $quotation->getScoreBreakdown($quotationsArray);
            $quotation->is_best_value = $quotation->isBestValue();
            return $quotation;
        });

        // Re-sort by score if requested
        if ($request->filled('sort_by') && $request->sort_by === 'score') {
            $scoredQuotations = $scoredQuotations->sortByDesc('score');
        }

        // Calculate comparison statistics
        $stats = [
            'total_quotations' => $scoredQuotations->count(),
            'min_price' => $scoredQuotations->min('total_price'),
            'max_price' => $scoredQuotations->max('total_price'),
            'avg_price' => $scoredQuotations->avg('total_price'),
            'price_range' => $scoredQuotations->max('total_price') - $scoredQuotations->min('total_price'),
            'best_score' => $scoredQuotations->max('score'),
            'avg_score' => $scoredQuotations->avg('score'),
        ];

        // Replace quotations collection with sorted/filtered and scored one
        $rfq->setRelation('quotations', $scoredQuotations);

        return view('buyer.quotations.compare', compact('rfq', 'stats'));
    }

    /**
     * Accept a quotation.
     * 
     * Refactored to use QuotationWorkflowService for business logic.
     * Controller is now thin - just authorization, delegation, and response.
     */
    public function accept(Request $request, Quotation $quotation, QuotationWorkflowService $workflowService): RedirectResponse
    {
        $this->authorize('accept', $quotation);

        $buyer = Auth::user()->buyerProfile;

        // Ensure quotation belongs to buyer's RFQ
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لقبول هذا العرض');
        }

        try {
            DB::beginTransaction();

            // Load quotation with necessary relationships before processing
            $quotation->load(['rfq', 'supplier', 'items.rfqItem']);

            // Delegate to workflow service (handles locking, state transitions, notifications)
            $quotation = $workflowService->acceptQuotation($quotation, Auth::user());

            // Create Order from accepted quotation
            $order = $this->createOrderFromQuotation($quotation, $buyer);

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

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            // Business rule violation (e.g., RFQ already awarded)
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer accept quotation error', [
                'quotation_id' => $quotation->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء قبول العرض: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a quotation.
     * 
     * Refactored to use QuotationWorkflowService for business logic.
     */
    public function reject(Request $request, Quotation $quotation, QuotationWorkflowService $workflowService): RedirectResponse
    {
        $this->authorize('reject', $quotation);

        $buyer = Auth::user()->buyerProfile;

        // Ensure quotation belongs to buyer's RFQ
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لرفض هذا العرض');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            // Delegate to workflow service
            $quotation = $workflowService->rejectQuotation(
                $quotation, 
                Auth::user(), 
                $validated['rejection_reason'] ?? null
            );

            return back()->with('success', 'تم رفض عرض السعر');

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Buyer reject quotation error', [
                'quotation_id' => $quotation->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء رفض العرض: ' . $e->getMessage()]);
        }
    }

    /**
     * Create an Order from accepted Quotation.
     */
    private function createOrderFromQuotation(Quotation $quotation, $buyer): Order
    {
        // Load quotation items with rfqItem relationship for fallback values
        $quotation->load('items.rfqItem');

        // Validate quotation has items
        if ($quotation->items->isEmpty()) {
            throw new \InvalidArgumentException('عرض السعر لا يحتوي على أي بنود. لا يمكن إنشاء طلب.');
        }

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
                'item_name' => $quotationItem->item_name ?? $quotationItem->rfqItem?->item_name ?? 'بند',
                'specifications' => $quotationItem->specifications ?? $quotationItem->rfqItem?->specifications,
                'quantity' => $quotationItem->quantity ?? $quotationItem->rfqItem?->quantity ?? 1,
                'unit' => $quotationItem->unit ?? $quotationItem->rfqItem?->unit ?? 'وحدة',
                'unit_price' => $quotationItem->unit_price,
                'subtotal' => $quotationItem->total_price ?? ($quotationItem->unit_price * ($quotationItem->quantity ?? $quotationItem->rfqItem?->quantity ?? 1)),
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_price' => $quotationItem->total_price ?? ($quotationItem->unit_price * ($quotationItem->quantity ?? $quotationItem->rfqItem?->quantity ?? 1)),
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

        // Send order confirmation email to buyer
        try {
            if ($buyer->user && $buyer->user->email) {
                \Illuminate\Support\Facades\Mail::to($buyer->user->email)
                    ->send(new \App\Mail\OrderConfirmation($order));
            }
        } catch (\Throwable $e) {
            // Log email error but don't fail the order creation
            Log::warning('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'buyer_email' => $buyer->user->email ?? 'N/A',
                'error' => $e->getMessage(),
            ]);
        }

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

