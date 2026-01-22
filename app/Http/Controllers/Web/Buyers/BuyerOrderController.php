<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\BuyerCart;
use App\Models\BuyerCartItem;
use App\Models\Order;
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

/**
 * Buyer Order Controller
 *
 * Handles order viewing for buyers.
 * Buyers can view their orders and track their status.
 * Note: Buyers cannot update order status - that's the supplier's responsibility.
 * 
 * Note: Buyer verification is handled by the 'buyer.verified' middleware.
 */
class BuyerOrderController extends Controller
{
    /**
     * Display list of orders for the buyer.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $buyer = Auth::user()->buyerProfile;

        $query = Order::with(['supplier.user', 'items', 'quotation.rfq'])
            ->where('buyer_id', $buyer->id);

        // Filter by status (supports multiple statuses)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Enhanced search across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sub) use ($search) {
                      $sub->where('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('quotation', fn($sub) => $sub->where('reference_code', 'like', "%{$search}%"));
            });
        }

        // Date range filter with quick filters
        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            match ($dateFilter) {
                'today' => $query->whereDate('order_date', today()),
                'this_week' => $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year),
                'last_month' => $query->whereMonth('order_date', now()->subMonth()->month)->whereYear('order_date', now()->subMonth()->year),
                default => null,
            };
        } else {
            // Custom date range
            if ($request->filled('date_from')) {
                $query->whereDate('order_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('order_date', '<=', $request->date_to);
            }
        }

        $orders = $query->latest('order_date')->paginate(15)->withQueryString();

        // Optimized stats calculation using single query
        $stats = Order::where('buyer_id', $buyer->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled,
                COALESCE(SUM(total_amount), 0) as total_spending
            ', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ])
            ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'processing' => $stats->processing ?? 0,
            'shipped' => $stats->shipped ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'total_spending' => $stats->total_spending ?? 0,
        ];

        // Log activity
        activity('buyer_orders')
            ->causedBy(Auth::user())
            ->withProperties([
                'buyer_id' => $buyer->id,
                'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
            ])
            ->log('عرض المشتري قائمة الطلبات');

        return view('buyer.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details.
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $buyer = Auth::user()->buyerProfile;

        $order->load([
            'supplier.user',
            'items.product',
            'quotation.rfq',
            'quotation.items',
            'invoices',
            'deliveries',
        ]);

        // Log activity
        activity('buyer_orders')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
            ])
            ->log('عرض المشتري تفاصيل الطلب: ' . $order->order_number);

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * Get status label in Arabic.
     */
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            Order::STATUS_PENDING => 'قيد الانتظار',
            Order::STATUS_PROCESSING => 'قيد المعالجة',
            Order::STATUS_SHIPPED => 'تم الشحن',
            Order::STATUS_DELIVERED => 'تم التسليم',
            Order::STATUS_CANCELLED => 'ملغى',
            default => $status,
        };
    }

    /**
     * Get status color for UI.
     */
    public static function getStatusColor(string $status): string
    {
        return match($status) {
            Order::STATUS_PENDING => 'yellow',
            Order::STATUS_PROCESSING => 'blue',
            Order::STATUS_SHIPPED => 'indigo',
            Order::STATUS_DELIVERED => 'green',
            Order::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /**
     * Quick re-order: Add order items to cart for re-ordering.
     */
    public function addToCart(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $order->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لإعادة طلب هذا الأمر');
        }

        DB::beginTransaction();

        try {
            // Get or create active cart
            $cart = BuyerCart::getOrCreateActive($buyer);

            // Load order items
            $order->load('items.product');

            $itemsAdded = 0;
            $itemsSkipped = 0;

            foreach ($order->items as $item) {
                // Check if product still exists and is active
                if (!$item->product || !$item->product->is_active || $item->product->review_status !== 'approved') {
                    $itemsSkipped++;
                    continue;
                }

                // Check if item already exists in cart
                $existingItem = BuyerCartItem::where('cart_id', $cart->id)
                    ->where('product_id', $item->product_id)
                    ->where('supplier_id', $order->supplier_id)
                    ->first();

                if ($existingItem) {
                    // Update quantity
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item->quantity,
                        'specifications' => $item->specifications ?? $existingItem->specifications,
                        'unit' => $item->unit ?? $existingItem->unit,
                    ]);
                } else {
                    // Create new cart item
                    BuyerCartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'specifications' => $item->specifications,
                        'unit' => $item->unit,
                        'supplier_id' => $order->supplier_id,
                    ]);
                }

                $itemsAdded++;
            }

            // Log activity
            activity('buyer_orders')
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'items_added' => $itemsAdded,
                    'items_skipped' => $itemsSkipped,
                ])
                ->log('قام المشتري بإضافة عناصر الطلب إلى السلة لإعادة الطلب');

            DB::commit();

            $message = "تم إضافة {$itemsAdded} منتج إلى السلة";
            if ($itemsSkipped > 0) {
                $message .= " ({$itemsSkipped} منتج غير متوفر تم تخطيه)";
            }

            return redirect()
                ->route('buyer.cart.index')
                ->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Reorder to cart error', [
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة المنتجات إلى السلة']);
        }
    }

    /**
     * Create RFQ directly from order (skip cart).
     */
    public function reorder(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $order->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لإعادة طلب هذا الأمر');
        }

        DB::beginTransaction();

        try {
            // Load order items and original quotation
            $order->load(['items.product', 'quotation.rfq']);

            // Create new RFQ based on the order
            $originalRfq = $order->quotation?->rfq;
            
            $rfq = Rfq::create([
                'buyer_id' => $buyer->id,
                'created_by' => Auth::id(),
                'title' => "إعادة طلب: {$order->order_number}",
                'description' => $originalRfq?->description ?? "إعادة طلب من الطلب رقم {$order->order_number}",
                'deadline' => now()->addDays(7), // Default 7 days
                'is_public' => $originalRfq?->is_public ?? true,
                'status' => 'draft',
                'reference_code' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_RFQ,
                    Rfq::class
                ),
            ]);

            // Create RFQ items from order items
            $itemsAdded = 0;
            $itemsSkipped = 0;

            foreach ($order->items as $item) {
                // Check if product still exists and is active
                if (!$item->product || !$item->product->is_active || $item->product->review_status !== 'approved') {
                    $itemsSkipped++;
                    continue;
                }

                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                ]);

                $itemsAdded++;
            }

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'reference_code' => $rfq->reference_code,
                    'original_order_id' => $order->id,
                    'original_order_number' => $order->order_number,
                    'items_added' => $itemsAdded,
                    'items_skipped' => $itemsSkipped,
                ])
                ->log('قام المشتري بإنشاء RFQ من طلب سابق (إعادة طلب)');

            DB::commit();

            $message = "تم إنشاء طلب عرض سعر جديد بنجاح";
            if ($itemsSkipped > 0) {
                $message .= " ({$itemsSkipped} منتج غير متوفر تم تخطيه)";
            }

            return redirect()
                ->route('buyer.rfqs.edit', $rfq)
                ->with('success', $message . '. يمكنك الآن مراجعة وتعديل الطلب قبل الإرسال.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Direct reorder error', [
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء طلب العرض']);
        }
    }

    /**
     * Show order history with re-order options and analytics.
     */
    public function history(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $buyer = Auth::user()->buyerProfile;

        // Get completed orders grouped by supplier
        $ordersBySupplier = Order::with(['supplier', 'items.product'])
            ->where('buyer_id', $buyer->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->get()
            ->groupBy('supplier_id');

        // Calculate analytics
        $analytics = [
            'total_orders' => Order::where('buyer_id', $buyer->id)->count(),
            'completed_orders' => Order::where('buyer_id', $buyer->id)
                ->where('status', Order::STATUS_DELIVERED)->count(),
            'total_spent' => Order::where('buyer_id', $buyer->id)
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('total_amount'),
            'avg_order_value' => Order::where('buyer_id', $buyer->id)
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->avg('total_amount'),
            'most_ordered_products' => $this->getMostOrderedProducts($buyer->id, 5),
            'favorite_suppliers' => $this->getFavoriteSuppliers($buyer->id, 3),
        ];

        // Recent orders for quick reorder
        $recentOrders = Order::with(['supplier.user', 'items.product'])
            ->where('buyer_id', $buyer->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->latest('order_date')
            ->take(10)
            ->get();

        return view('buyer.orders.history', compact('ordersBySupplier', 'analytics', 'recentOrders'));
    }

    /**
     * Get most ordered products for analytics.
     */
    private function getMostOrderedProducts(int $buyerId, int $limit = 5): array
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.buyer_id', $buyerId)
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get favorite suppliers for analytics.
     */
    private function getFavoriteSuppliers(int $buyerId, int $limit = 3): array
    {
        return DB::table('orders')
            ->join('suppliers', 'orders.supplier_id', '=', 'suppliers.id')
            ->where('orders.buyer_id', $buyerId)
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'suppliers.id',
                'suppliers.company_name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->groupBy('suppliers.id', 'suppliers.company_name')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}

