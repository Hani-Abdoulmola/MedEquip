<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Admin Reports Controller
 *
 * Provides platform-wide analytics and reports for administrators.
 */
class AdminReportsController extends Controller
{
    /**
     * Display admin reports and analytics dashboard.
     */
    public function index(Request $request): View
    {
        // Date range (default to last 30 days)
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        // Platform Overview
        $platformOverview = $this->getPlatformOverview($fromDate, $toDate);

        // User Statistics
        $userStats = $this->getUserStatistics($fromDate, $toDate);

        // Order Statistics
        $orderStats = $this->getOrderStatistics($fromDate, $toDate);

        // RFQ/Quotation Statistics
        $rfqStats = $this->getRfqStatistics($fromDate, $toDate);

        // Revenue Trends (Monthly for last 6 months)
        $revenueTrends = $this->getRevenueTrends();

        // Top Suppliers
        $topSuppliers = $this->getTopSuppliers($fromDate, $toDate);

        // Top Buyers
        $topBuyers = $this->getTopBuyers($fromDate, $toDate);

        // Top Products
        $topProducts = $this->getTopProducts($fromDate, $toDate);

        // Payment Statistics
        $paymentStats = $this->getPaymentStatistics($fromDate, $toDate);

        // Delivery Statistics
        $deliveryStats = $this->getDeliveryStatistics($fromDate, $toDate);

        // Log activity
        activity('admin_reports')
            ->causedBy(Auth::user())
            ->withProperties([
                'date_range' => ['from' => $fromDate, 'to' => $toDate],
            ])
            ->log('عرض الأدمن التقارير والتحليلات');

        return view('admin.reports.index', compact(
            'platformOverview',
            'userStats',
            'orderStats',
            'rfqStats',
            'revenueTrends',
            'topSuppliers',
            'topBuyers',
            'topProducts',
            'paymentStats',
            'deliveryStats',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Get platform overview statistics.
     */
    private function getPlatformOverview(string $fromDate, string $toDate): array
    {
        // Total revenue from completed orders
        $totalRevenue = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->sum('total_amount');

        // Total orders
        $totalOrders = Order::whereBetween('order_date', [$fromDate, $toDate])->count();

        // Completed orders
        $completedOrders = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->count();

        // Conversion rate (RFQ to Order)
        $totalRfqs = Rfq::whereBetween('created_at', [$fromDate, $toDate])->count();
        $rfqsWithOrders = Rfq::whereBetween('created_at', [$fromDate, $toDate])
            ->whereHas('quotations', function ($q) {
                $q->where('status', 'accepted')
                  ->whereHas('orders');
            })
            ->count();

        $conversionRate = $totalRfqs > 0 ? round(($rfqsWithOrders / $totalRfqs) * 100, 2) : 0;

        // Average order value
        $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        // Previous period comparison (for trends)
        $prevFromDate = date('Y-m-d', strtotime($fromDate . ' -30 days'));
        $prevToDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $prevRevenue = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('order_date', [$prevFromDate, $prevToDate])
            ->sum('total_amount');
        $prevOrders = Order::whereBetween('order_date', [$prevFromDate, $prevToDate])->count();

        $revenueGrowth = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;
        $ordersGrowth = $prevOrders > 0 ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1) : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'conversion_rate' => $conversionRate,
            'average_order_value' => $avgOrderValue,
            'revenue_growth' => $revenueGrowth,
            'orders_growth' => $ordersGrowth,
        ];
    }

    /**
     * Get user statistics.
     */
    private function getUserStatistics(string $fromDate, string $toDate): array
    {
        // Total users
        $totalUsers = User::count();

        // New users in period
        $newUsers = User::whereBetween('created_at', [$fromDate, $toDate])->count();

        // Suppliers
        $totalSuppliers = Supplier::count();
        $newSuppliers = Supplier::whereBetween('created_at', [$fromDate, $toDate])->count();
        $verifiedSuppliers = Supplier::where('is_verified', true)->count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        // Buyers
        $totalBuyers = Buyer::count();
        $newBuyers = Buyer::whereBetween('created_at', [$fromDate, $toDate])->count();
        $verifiedBuyers = Buyer::where('is_verified', true)->count();
        $activeBuyers = Buyer::where('is_active', true)->count();

        // Previous period comparison
        $prevFromDate = date('Y-m-d', strtotime($fromDate . ' -30 days'));
        $prevToDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $prevNewUsers = User::whereBetween('created_at', [$prevFromDate, $prevToDate])->count();

        $usersGrowth = $prevNewUsers > 0 ? round((($newUsers - $prevNewUsers) / $prevNewUsers) * 100, 1) : 0;

        return [
            'total_users' => $totalUsers,
            'new_users' => $newUsers,
            'users_growth' => $usersGrowth,
            'total_suppliers' => $totalSuppliers,
            'new_suppliers' => $newSuppliers,
            'verified_suppliers' => $verifiedSuppliers,
            'active_suppliers' => $activeSuppliers,
            'total_buyers' => $totalBuyers,
            'new_buyers' => $newBuyers,
            'verified_buyers' => $verifiedBuyers,
            'active_buyers' => $activeBuyers,
        ];
    }

    /**
     * Get order statistics.
     */
    private function getOrderStatistics(string $fromDate, string $toDate): array
    {
        $stats = Order::whereBetween('order_date', [$fromDate, $toDate])
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
     * Get RFQ and Quotation statistics.
     */
    private function getRfqStatistics(string $fromDate, string $toDate): array
    {
        // RFQ Stats
        $totalRfqs = Rfq::whereBetween('created_at', [$fromDate, $toDate])->count();
        $openRfqs = Rfq::where('status', 'open')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $closedRfqs = Rfq::where('status', 'closed')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        // Quotation Stats
        $totalQuotations = Quotation::whereBetween('created_at', [$fromDate, $toDate])->count();
        $pendingQuotations = Quotation::where('status', 'pending')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $acceptedQuotations = Quotation::where('status', 'accepted')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $rejectedQuotations = Quotation::where('status', 'rejected')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        // Acceptance rate
        $acceptanceRate = $totalQuotations > 0 ? round(($acceptedQuotations / $totalQuotations) * 100, 2) : 0;

        return [
            'total_rfqs' => $totalRfqs,
            'open_rfqs' => $openRfqs,
            'closed_rfqs' => $closedRfqs,
            'total_quotations' => $totalQuotations,
            'pending_quotations' => $pendingQuotations,
            'accepted_quotations' => $acceptedQuotations,
            'rejected_quotations' => $rejectedQuotations,
            'acceptance_rate' => $acceptanceRate,
        ];
    }

    /**
     * Get revenue trends (monthly for last 6 months).
     */
    private function getRevenueTrends(): array
    {
        $revenue = Order::where('status', Order::STATUS_DELIVERED)
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
     * Get top suppliers by revenue.
     */
    private function getTopSuppliers(string $fromDate, string $toDate, int $limit = 10): array
    {
        $supplierStats = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->select('supplier_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('supplier_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $supplierStats->map(function ($stat) {
            $supplier = Supplier::find($stat->supplier_id);
            return [
                'supplier_name' => $supplier?->company_name ?? 'غير معروف',
                'product_count' => $supplier?->products()->count() ?? 0,
                'order_count' => $stat->order_count,
                'total_revenue' => $stat->total_revenue,
            ];
        })->toArray();
    }

    /**
     * Get top buyers by spending.
     */
    private function getTopBuyers(string $fromDate, string $toDate, int $limit = 10): array
    {
        $buyerStats = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->select('buyer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spending'))
            ->groupBy('buyer_id')
            ->orderByDesc('total_spending')
            ->limit($limit)
            ->get();

        return $buyerStats->map(function ($stat) {
            $buyer = Buyer::find($stat->buyer_id);
            return [
                'buyer_name' => $buyer?->organization_name ?? 'غير معروف',
                'order_count' => $stat->order_count,
                'total_spending' => $stat->total_spending,
            ];
        })->toArray();
    }

    /**
     * Get top products by sales.
     */
    private function getTopProducts(string $fromDate, string $toDate, int $limit = 10): array
    {
        $productStats = OrderItem::whereHas('order', function ($q) use ($fromDate, $toDate) {
            $q->where('status', Order::STATUS_DELIVERED)
              ->whereBetween('order_date', [$fromDate, $toDate]);
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_revenue'))
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->limit($limit)
        ->get();

        return $productStats->map(function ($stat) {
            $product = Product::find($stat->product_id);
            return [
                'name' => $product?->name ?? 'منتج محذوف',
                'total_quantity' => $stat->total_quantity ?? 0,
                'total_revenue' => $stat->total_revenue ?? 0,
            ];
        })->toArray();
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStatistics(string $fromDate, string $toDate): array
    {
        $stats = Payment::whereBetween('paid_at', [$fromDate, $toDate])
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
    private function getDeliveryStatistics(string $fromDate, string $toDate): array
    {
        $stats = Delivery::whereBetween('delivery_date', [$fromDate, $toDate])
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

