<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierPerformanceMetric;
use App\Models\SupplierReview;
use App\Models\Order;
use App\Models\Rfq;
use App\Models\Quotation;
use App\Models\DeliveryReview;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierPerformanceService
{
    /**
     * Calculate and store performance metrics for a supplier for a given period.
     */
    public function calculateMetrics(Supplier $supplier, Carbon $periodStart, Carbon $periodEnd): SupplierPerformanceMetric
    {
        $metrics = [
            'supplier_id' => $supplier->id,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ];

        // Review metrics
        $reviews = SupplierReview::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->approved()
            ->get();

        $metrics['total_reviews'] = $reviews->count();
        $metrics['average_rating'] = $reviews->avg('overall_rating') ?? 0;

        // Order metrics
        $orders = Order::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->get();

        $metrics['total_orders'] = $orders->count();
        $metrics['completed_orders'] = $orders->where('status', 'completed')->count();
        $metrics['cancelled_orders'] = $orders->where('status', 'cancelled')->count();
        $metrics['total_revenue'] = $orders->where('status', 'completed')->sum('total_amount');
        $metrics['average_order_value'] = $metrics['total_orders'] > 0
            ? $metrics['total_revenue'] / $metrics['completed_orders']
            : 0;

        // Performance metrics
        $deliveryReviews = DeliveryReview::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->approved()
            ->get();

        $onTimeDeliveries = $deliveryReviews->where('timeliness_rating', '>=', 4)->count();
        $metrics['on_time_delivery_rate'] = $deliveryReviews->count() > 0
            ? ($onTimeDeliveries / $deliveryReviews->count()) * 100
            : 0;

        // RFQ/Quotation metrics
        $rfqs = Rfq::whereHas('suppliers', function ($q) use ($supplier) {
            $q->where('suppliers.id', $supplier->id);
        })->whereBetween('created_at', [$periodStart, $periodEnd])->get();

        $quotations = Quotation::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->get();

        $metrics['rfqs_received'] = $rfqs->count();
        $metrics['quotations_submitted'] = $quotations->count();
        $metrics['response_rate'] = $metrics['rfqs_received'] > 0
            ? ($metrics['quotations_submitted'] / $metrics['rfqs_received']) * 100
            : 0;

        // Win rate (quotations that became orders)
        $metrics['orders_won'] = $quotations->whereIn('id', $orders->pluck('quotation_id'))->count();
        $metrics['win_rate'] = $metrics['quotations_submitted'] > 0
            ? ($metrics['orders_won'] / $metrics['quotations_submitted']) * 100
            : 0;

        // Response time
        $responseTimes = $quotations->whereNotNull('submitted_at')
            ->map(function ($quote) {
                return $quote->rfq->created_at->diffInHours($quote->submitted_at);
            });
        $metrics['average_response_time_hours'] = $responseTimes->avg() ?? 0;

        // Delivery time
        $completedOrders = $orders->where('status', 'completed');
        $deliveryTimes = $completedOrders->map(function ($order) {
            return optional($order->delivery)->created_at?->diffInDays($order->delivery->delivered_at) ?? 0;
        });
        $metrics['average_delivery_days'] = $deliveryTimes->avg() ?? 0;

        // Quality metrics
        $metrics['quality_issues_count'] = 0; // Can be enhanced with product reviews
        $metrics['returns_count'] = 0; // Can be enhanced with return system
        $metrics['disputes_count'] = 0; // Can be enhanced with disputes

        // Fulfillment rate
        $metrics['fulfillment_rate'] = $metrics['total_orders'] > 0
            ? ($metrics['completed_orders'] / $metrics['total_orders']) * 100
            : 0;

        // Calculate overall score (weighted)
        $metrics['overall_score'] = $this->calculateOverallScore($metrics);

        // Assign badges
        $metrics['badges'] = $this->assignBadges($metrics, $reviews);

        // Create or update metric record
        return SupplierPerformanceMetric::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            $metrics
        );
    }

    /**
     * Calculate overall performance score (0-100).
     */
    protected function calculateOverallScore(array $metrics): float
    {
        $scores = [];

        // Rating score (30%)
        if ($metrics['total_reviews'] > 0) {
            $scores[] = ($metrics['average_rating'] / 5) * 30;
        }

        // Response rate (20%)
        $scores[] = ($metrics['response_rate'] / 100) * 20;

        // Fulfillment rate (20%)
        $scores[] = ($metrics['fulfillment_rate'] / 100) * 20;

        // On-time delivery (20%)
        $scores[] = ($metrics['on_time_delivery_rate'] / 100) * 20;

        // Win rate (10%)
        $scores[] = ($metrics['win_rate'] / 100) * 10;

        return round(array_sum($scores), 2);
    }

    /**
     * Assign performance badges based on metrics.
     */
    protected function assignBadges(array $metrics, $reviews): array
    {
        $badges = [];

        // Top Rated
        if ($metrics['average_rating'] >= 4.5 && $metrics['total_reviews'] >= 10) {
            $badges[] = 'top_rated';
        }

        // Fast Delivery
        if ($metrics['on_time_delivery_rate'] >= 95 && $metrics['completed_orders'] >= 5) {
            $badges[] = 'fast_delivery';
        }

        // Quality Assured
        if ($metrics['average_rating'] >= 4.0 && $metrics['quality_issues_count'] == 0) {
            $badges[] = 'quality_assured';
        }

        // Responsive
        if ($metrics['response_rate'] >= 90 && $metrics['average_response_time_hours'] < 24) {
            $badges[] = 'responsive';
        }

        // Reliable
        if ($metrics['fulfillment_rate'] >= 95 && $metrics['completed_orders'] >= 10) {
            $badges[] = 'reliable';
        }

        // Best Value
        $recommendCount = $reviews->where('would_recommend', true)->count();
        if ($recommendCount >= 5 && ($recommendCount / max($metrics['total_reviews'], 1)) >= 0.9) {
            $badges[] = 'best_value';
        }

        // Verified (if supplier is verified)
        // This would check supplier verification status

        // Rising Star (new suppliers performing well)
        if ($metrics['overall_score'] >= 80 && $metrics['total_orders'] >= 3 && $metrics['total_orders'] < 20) {
            $badges[] = 'rising_star';
        }

        return $badges;
    }

    /**
     * Calculate rankings for all suppliers.
     */
    public function calculateRankings(Carbon $periodStart, Carbon $periodEnd): void
    {
        // Overall ranking
        $metrics = SupplierPerformanceMetric::whereBetween('period_start', [$periodStart, $periodEnd])
            ->orderBy('overall_score', 'desc')
            ->get();

        foreach ($metrics as $index => $metric) {
            $metric->update(['overall_rank' => $index + 1]);
        }

        // Category ranking (would need to group by supplier categories)
        // This can be enhanced based on supplier categories
    }

    /**
     * Get top performers for a period.
     */
    public function getTopPerformers(Carbon $periodStart, Carbon $periodEnd, int $limit = 10)
    {
        return SupplierPerformanceMetric::with('supplier')
            ->whereBetween('period_start', [$periodStart, $periodEnd])
            ->where('overall_score', '>=', 80)
            ->orderBy('overall_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get supplier dashboard data.
     */
    public function getSupplierDashboard(Supplier $supplier): array
    {
        $currentMonth = SupplierPerformanceMetric::where('supplier_id', $supplier->id)
            ->whereBetween('period_start', [now()->startOfMonth(), now()->endOfMonth()])
            ->first();

        $lastMonth = SupplierPerformanceMetric::where('supplier_id', $supplier->id)
            ->whereBetween('period_start', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->first();

        return [
            'current' => $currentMonth,
            'previous' => $lastMonth,
            'trend' => $this->calculateTrend($currentMonth, $lastMonth),
            'badges' => $currentMonth?->badges ?? [],
            'rank' => $currentMonth?->overall_rank,
        ];
    }

    /**
     * Calculate performance trend.
     */
    protected function calculateTrend($current, $previous): array
    {
        if (!$current || !$previous) {
            return [];
        }

        return [
            'score' => $current->overall_score - $previous->overall_score,
            'rating' => $current->average_rating - $previous->average_rating,
            'orders' => $current->total_orders - $previous->total_orders,
            'revenue' => $current->total_revenue - $previous->total_revenue,
        ];
    }
}
