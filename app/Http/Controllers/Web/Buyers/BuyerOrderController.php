<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BuyerOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Buyer Order Controller (Phase 1: delegates to BuyerOrderService)
 */
class BuyerOrderController extends Controller
{
    public function __construct(
        protected BuyerOrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);
        $buyer = Auth::user()->buyerProfile;

        $filters = $request->only(['status', 'search', 'date_filter', 'date_from', 'date_to']);
        $orders = $this->orderService->getOrders($buyer, $filters, 15);
        $stats = $this->orderService->getOrderStats($buyer);

        activity('buyer_orders')
            ->causedBy(Auth::user())
            ->withProperties(['buyer_id' => $buyer->id, 'filters' => $filters])
            ->log('عرض المشتري قائمة الطلبات');

        return view('buyer.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);
        $buyer = Auth::user()->buyerProfile;

        $order = $this->orderService->getOrderDetails($order->id, $buyer);

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

    public function addToCart(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $order->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لإعادة طلب هذا الأمر');
        }

        try {
            $result = $this->orderService->reorderToBuilder($order, $buyer);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة المنتجات إلى منشئ الطلبات']);
        }

        activity('buyer_orders')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'items_added' => $result['added'],
                'items_skipped' => $result['skipped'],
            ])
            ->log('قام المشتري بإضافة عناصر الطلب إلى منشئ الطلبات لإعادة الطلب');

        $redirect = redirect()->route('buyer.cart.index')->with('success', $result['message']);
        if (!empty($result['skipped_items'])) {
            $redirect->with('skipped_items', $result['skipped_items']);
        }
        return $redirect;
    }

    public function reorder(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $order->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لإعادة طلب هذا الأمر');
        }

        try {
            $result = $this->orderService->reorderToRfq($order, $buyer);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء طلب العرض']);
        }

        $message = $result['message'] . '. يمكنك الآن مراجعة وتعديل الطلب قبل الإرسال.';
        return redirect()->route('buyer.rfqs.edit', $result['rfq'])->with('success', $message);
    }

    /**
     * Cancel pending order (Phase 2).
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $order->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لإلغاء هذا الطلب');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'يمكن إلغاء الطلبات المعلقة فقط.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'notes' => ($order->notes ? $order->notes . "\n\n" : '') . 'تم الإلغاء من قبل المشتري' . ($validated['cancellation_reason'] ? ': ' . $validated['cancellation_reason'] : ''),
            ]);
        });

        activity('buyer_orders')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $validated['cancellation_reason'] ?? null,
            ])
            ->log('قام المشتري بإلغاء الطلب');

        return redirect()->route('buyer.orders.show', $order)
            ->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    public function history(Request $request): View
    {
        $this->authorize('viewAny', Order::class);
        $buyer = Auth::user()->buyerProfile;

        $ordersBySupplier = Order::with(['supplier', 'items.product'])
            ->where('buyer_id', $buyer->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->get()
            ->groupBy('supplier_id');

        $analytics = [
            'total_orders' => Order::where('buyer_id', $buyer->id)->count(),
            'completed_orders' => Order::where('buyer_id', $buyer->id)->where('status', Order::STATUS_DELIVERED)->count(),
            'total_spent' => Order::where('buyer_id', $buyer->id)->where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount'),
            'avg_order_value' => Order::where('buyer_id', $buyer->id)->where('status', '!=', Order::STATUS_CANCELLED)->avg('total_amount'),
            'most_ordered_products' => $this->getMostOrderedProducts($buyer->id, 5),
            'favorite_suppliers' => $this->getFavoriteSuppliers($buyer->id, 3),
        ];

        $recentOrders = Order::with(['supplier.user', 'items.product'])
            ->where('buyer_id', $buyer->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->latest('order_date')
            ->take(10)
            ->get();

        return view('buyer.orders.history', compact('ordersBySupplier', 'analytics', 'recentOrders'));
    }

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
