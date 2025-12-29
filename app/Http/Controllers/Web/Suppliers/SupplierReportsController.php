<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Supplier Reports Controller
 *
 * Provides advanced analytics and reports for suppliers.
 */
class SupplierReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensure_supplier_profile');
    }

    /**
     * Display supplier reports and analytics dashboard.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Date range (default to last 30 days)
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        // Sales Overview
        $salesOverview = $this->getSalesOverview($supplier->id, $fromDate, $toDate);

        // Order Statistics
        $orderStats = $this->getOrderStatistics($supplier->id, $fromDate, $toDate);

        // Quotation Statistics
        $quotationStats = $this->getQuotationStatistics($supplier->id, $fromDate, $toDate);

        // Revenue Trends (Monthly for last 6 months)
        $revenueTrends = $this->getRevenueTrends($supplier->id);

        // Top Buyers
        $topBuyers = $this->getTopBuyers($supplier->id, $fromDate, $toDate);

        // Product Performance
        $productPerformance = $this->getProductPerformance($supplier->id, $fromDate, $toDate);

        // Payment Statistics
        $paymentStats = $this->getPaymentStatistics($supplier->id, $fromDate, $toDate);

        // Delivery Statistics
        $deliveryStats = $this->getDeliveryStatistics($supplier->id, $fromDate, $toDate);

        // Log activity
        activity('supplier_reports')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'date_range' => ['from' => $fromDate, 'to' => $toDate],
            ])
            ->log('عرض المورد التقارير والتحليلات');

        return view('supplier.reports.index', compact(
            'salesOverview',
            'orderStats',
            'quotationStats',
            'revenueTrends',
            'topBuyers',
            'productPerformance',
            'paymentStats',
            'deliveryStats',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Get sales overview statistics.
     */
    private function getSalesOverview(int $supplierId, string $fromDate, string $toDate): array
    {
        $stats = Order::where('supplier_id', $supplierId)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(AVG(total_amount), 0) as average_order_value,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_orders,
                COALESCE(SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END), 0) as completed_revenue
            ', [Order::STATUS_DELIVERED, Order::STATUS_DELIVERED])
            ->first();

        return [
            'total_orders' => $stats->total_orders ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
            'average_order_value' => $stats->average_order_value ?? 0,
            'completed_orders' => $stats->completed_orders ?? 0,
            'completed_revenue' => $stats->completed_revenue ?? 0,
        ];
    }

    /**
     * Get order statistics.
     */
    private function getOrderStatistics(int $supplierId, string $fromDate, string $toDate): array
    {
        $stats = Order::where('supplier_id', $supplierId)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->selectRaw('
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled
            ', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ])
            ->first();

        return [
            'pending' => $stats->pending ?? 0,
            'processing' => $stats->processing ?? 0,
            'shipped' => $stats->shipped ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
        ];
    }

    /**
     * Get quotation statistics.
     */
    private function getQuotationStatistics(int $supplierId, string $fromDate, string $toDate): array
    {
        $stats = Quotation::where('supplier_id', $supplierId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected,
                COALESCE(SUM(CASE WHEN status = ? THEN total_price ELSE 0 END), 0) as accepted_value
            ', ['pending', 'accepted', 'rejected', 'accepted'])
            ->first();

        $total = $stats->total ?? 0;
        $accepted = $stats->accepted ?? 0;

        return [
            'total' => $total,
            'pending' => $stats->pending ?? 0,
            'accepted' => $accepted,
            'rejected' => $stats->rejected ?? 0,
            'accepted_value' => $stats->accepted_value ?? 0,
            'success_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get revenue trends (monthly for last 6 months).
     */
    private function getRevenueTrends(int $supplierId): array
    {
        $revenue = Order::where('supplier_id', $supplierId)
            ->where('status', Order::STATUS_DELIVERED)
            ->where('order_date', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $trends[] = [
                'month' => now()->subMonths($i)->format('M Y'),
                'revenue' => (float) ($revenue[$month] ?? 0),
            ];
        }

        return $trends;
    }

    /**
     * Get top buyers by revenue.
     */
    private function getTopBuyers(int $supplierId, string $fromDate, string $toDate, int $limit = 10): array
    {
        return Order::where('supplier_id', $supplierId)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->where('status', Order::STATUS_DELIVERED)
            ->with('buyer')
            ->select('buyer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('buyer_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'buyer_name' => $order->buyer?->organization_name ?? 'غير معروف',
                    'order_count' => $order->order_count,
                    'total_revenue' => $order->total_revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Get product performance statistics.
     */
    private function getProductPerformance(int $supplierId, string $fromDate, string $toDate, int $limit = 10): array
    {
        return Product::whereHas('suppliers', function ($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        })
        ->whereHas('orderItems.order', function ($q) use ($supplierId, $fromDate, $toDate) {
            $q->where('supplier_id', $supplierId)
              ->whereBetween('order_date', [$fromDate, $toDate])
              ->where('status', Order::STATUS_DELIVERED);
        })
        ->withCount(['orderItems as total_quantity' => function ($q) use ($supplierId, $fromDate, $toDate) {
            $q->whereHas('order', function ($sub) use ($supplierId, $fromDate, $toDate) {
                $sub->where('supplier_id', $supplierId)
                    ->whereBetween('order_date', [$fromDate, $toDate])
                    ->where('status', Order::STATUS_DELIVERED);
            })->select(DB::raw('SUM(quantity)'));
        }])
        ->orderByDesc('total_quantity')
        ->limit($limit)
        ->get()
        ->map(function ($product) {
            return [
                'name' => $product->name,
                'total_quantity' => $product->total_quantity ?? 0,
            ];
        })
        ->toArray();
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStatistics(int $supplierId, string $fromDate, string $toDate): array
    {
        $stats = Payment::where('supplier_id', $supplierId)
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(amount), 0) as total_amount,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
            ', [
                'completed',
                'pending',
                'failed',
            ])
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'completed' => $stats->completed ?? 0,
            'pending' => $stats->pending ?? 0,
            'failed' => $stats->failed ?? 0,
        ];
    }

    /**
     * Get delivery statistics.
     */
    private function getDeliveryStatistics(int $supplierId, string $fromDate, string $toDate): array
    {
        $stats = Delivery::where('supplier_id', $supplierId)
            ->whereBetween('delivery_date', [$fromDate, $toDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
            ', [
                Delivery::STATUS_PENDING,
                Delivery::STATUS_IN_TRANSIT,
                Delivery::STATUS_DELIVERED,
                Delivery::STATUS_FAILED,
            ])
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'in_transit' => $stats->in_transit ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'failed' => $stats->failed ?? 0,
        ];
    }
}

