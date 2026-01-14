<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Rfq;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Buyer Reports Controller
 *
 * Provides analytics and reporting for buyer activities.
 * Includes spending trends, supplier performance, and procurement insights.
 */
class BuyerReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request): View
    {
        $buyer = Auth::user()->buyerProfile;

        // Date range filter
        $period = $request->get('period', 'this_month');
        $dateRange = $this->getDateRange($period, $request);

        // Spending summary
        $spendingSummary = $this->getSpendingSummary($buyer->id, $dateRange);

        // Monthly spending trend (last 12 months)
        $monthlySpending = $this->getMonthlySpending($buyer->id);

        // Spending by category
        $spendingByCategory = $this->getSpendingByCategory($buyer->id, $dateRange);

        // Top suppliers by spending
        $topSuppliers = $this->getTopSuppliers($buyer->id, $dateRange);

        // RFQ statistics
        $rfqStats = $this->getRfqStats($buyer->id, $dateRange);

        // Quotation analysis
        $quotationStats = $this->getQuotationStats($buyer->id, $dateRange);

        // Order fulfillment metrics
        $fulfillmentMetrics = $this->getFulfillmentMetrics($buyer->id, $dateRange);

        return view('buyer.reports.index', compact(
            'spendingSummary',
            'monthlySpending',
            'spendingByCategory',
            'topSuppliers',
            'rfqStats',
            'quotationStats',
            'fulfillmentMetrics',
            'period',
            'dateRange'
        ));
    }

    /**
     * Get date range based on period filter.
     */
    private function getDateRange(string $period, Request $request): array
    {
        $startDate = null;
        $endDate = now();

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case 'this_week':
                $startDate = now()->startOfWeek();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $startDate = now()->startOfQuarter();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') 
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : now()->subMonth();
                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : now();
                break;
            default:
                $startDate = now()->startOfMonth();
        }

        return [
            'start' => $startDate,
            'end' => $endDate,
            'label' => $this->getPeriodLabel($period, $startDate, $endDate),
        ];
    }

    /**
     * Get human-readable period label.
     */
    private function getPeriodLabel(string $period, $startDate, $endDate): string
    {
        return match ($period) {
            'today' => 'اليوم',
            'this_week' => 'هذا الأسبوع',
            'this_month' => 'هذا الشهر',
            'last_month' => 'الشهر الماضي',
            'this_quarter' => 'هذا الربع',
            'this_year' => 'هذه السنة',
            'custom' => $startDate->format('Y/m/d') . ' - ' . $endDate->format('Y/m/d'),
            default => 'هذا الشهر',
        };
    }

    /**
     * Get spending summary for the period.
     */
    private function getSpendingSummary(int $buyerId, array $dateRange): array
    {
        $currentPeriod = Order::where('buyer_id', $buyerId)
            ->whereBetween('order_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_spending,
                COALESCE(AVG(total_amount), 0) as avg_order_value,
                COUNT(DISTINCT supplier_id) as unique_suppliers
            ')
            ->first();

        // Previous period for comparison
        $periodDays = $dateRange['start']->diffInDays($dateRange['end']);
        $previousStart = $dateRange['start']->copy()->subDays($periodDays);
        $previousEnd = $dateRange['start']->copy()->subDay();

        $previousPeriod = Order::where('buyer_id', $buyerId)
            ->whereBetween('order_date', [$previousStart, $previousEnd])
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_spending')
            ->first();

        $spendingChange = $previousPeriod->total_spending > 0
            ? (($currentPeriod->total_spending - $previousPeriod->total_spending) / $previousPeriod->total_spending) * 100
            : ($currentPeriod->total_spending > 0 ? 100 : 0);

        return [
            'total_spending' => $currentPeriod->total_spending,
            'total_orders' => $currentPeriod->total_orders,
            'avg_order_value' => $currentPeriod->avg_order_value,
            'unique_suppliers' => $currentPeriod->unique_suppliers,
            'spending_change' => round($spendingChange, 1),
            'previous_spending' => $previousPeriod->total_spending,
        ];
    }

    /**
     * Get monthly spending trend for the last 12 months.
     */
    private function getMonthlySpending(int $buyerId): array
    {
        $data = Order::where('buyer_id', $buyerId)
            ->where('order_date', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw('
                YEAR(order_date) as year,
                MONTH(order_date) as month,
                COALESCE(SUM(total_amount), 0) as total
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];
        $arabicMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        // Fill in all 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            
            $labels[] = $arabicMonths[$month - 1] . ' ' . $year;
            
            $found = $data->first(fn($item) => $item->year == $year && $item->month == $month);
            $values[] = $found ? round($found->total, 2) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    /**
     * Get spending breakdown by product category.
     */
    private function getSpendingByCategory(int $buyerId, array $dateRange): array
    {
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('orders.buyer_id', $buyerId)
            ->whereBetween('orders.order_date', [$dateRange['start'], $dateRange['end']])
            ->whereNull('orders.deleted_at')
            ->selectRaw('
                product_categories.name as category_name,
                COALESCE(SUM(order_items.total_price), 0) as total_amount,
                COUNT(DISTINCT orders.id) as order_count
            ')
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get top suppliers by spending.
     */
    private function getTopSuppliers(int $buyerId, array $dateRange): array
    {
        return Order::where('buyer_id', $buyerId)
            ->whereBetween('order_date', [$dateRange['start'], $dateRange['end']])
            ->with('supplier:id,company_name')
            ->selectRaw('
                supplier_id,
                COUNT(*) as order_count,
                COALESCE(SUM(total_amount), 0) as total_amount,
                COALESCE(AVG(total_amount), 0) as avg_order_value
            ')
            ->groupBy('supplier_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'supplier_name' => $item->supplier->company_name ?? 'غير معروف',
                    'order_count' => $item->order_count,
                    'total_amount' => $item->total_amount,
                    'avg_order_value' => $item->avg_order_value,
                ];
            })
            ->toArray();
    }

    /**
     * Get RFQ statistics.
     */
    private function getRfqStats(int $buyerId, array $dateRange): array
    {
        $stats = Rfq::where('buyer_id', $buyerId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = "open" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed,
                SUM(CASE WHEN status = "awarded" THEN 1 ELSE 0 END) as awarded,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
            ')
            ->first();

        // Response rate (RFQs that received at least one quotation)
        $rfqsWithQuotations = Rfq::where('buyer_id', $buyerId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->has('quotations')
            ->count();

        $responseRate = $stats->total > 0 
            ? round(($rfqsWithQuotations / $stats->total) * 100, 1) 
            : 0;

        return [
            'total' => $stats->total ?? 0,
            'draft' => $stats->draft ?? 0,
            'open' => $stats->open ?? 0,
            'closed' => $stats->closed ?? 0,
            'awarded' => $stats->awarded ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'conversion_rate' => $stats->total > 0 
                ? round((($stats->awarded ?? 0) / $stats->total) * 100, 1) 
                : 0,
            'response_rate' => $responseRate,
        ];
    }

    /**
     * Get quotation analysis.
     */
    private function getQuotationStats(int $buyerId, array $dateRange): array
    {
        $stats = Quotation::whereHas('rfq', function ($q) use ($buyerId) {
            $q->where('buyer_id', $buyerId);
        })
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                COALESCE(AVG(total_amount), 0) as avg_amount,
                COALESCE(MIN(total_amount), 0) as min_amount,
                COALESCE(MAX(total_amount), 0) as max_amount
            ')
            ->first();

        return [
            'total_received' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'accepted' => $stats->accepted ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'acceptance_rate' => $stats->total > 0 
                ? round((($stats->accepted ?? 0) / $stats->total) * 100, 1) 
                : 0,
            'avg_quote_amount' => $stats->avg_amount ?? 0,
            'min_quote_amount' => $stats->min_amount ?? 0,
            'max_quote_amount' => $stats->max_amount ?? 0,
        ];
    }

    /**
     * Get order fulfillment metrics.
     */
    private function getFulfillmentMetrics(int $buyerId, array $dateRange): array
    {
        $stats = Order::where('buyer_id', $buyerId)
            ->whereBetween('order_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status IN ("pending", "processing", "shipped") THEN 1 ELSE 0 END) as in_progress
            ')
            ->first();

        return [
            'total_orders' => $stats->total ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'in_progress' => $stats->in_progress ?? 0,
            'fulfillment_rate' => $stats->total > 0 
                ? round((($stats->delivered ?? 0) / $stats->total) * 100, 1) 
                : 0,
            'cancellation_rate' => $stats->total > 0 
                ? round((($stats->cancelled ?? 0) / $stats->total) * 100, 1) 
                : 0,
        ];
    }
}

