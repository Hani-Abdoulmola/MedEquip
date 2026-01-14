<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}

