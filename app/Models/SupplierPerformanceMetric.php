<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Supplier Performance Metrics Model
 *
 * Stores calculated performance metrics for suppliers over time periods.
 *
 * @property int $id
 * @property int $supplier_id
 * @property \Carbon\Carbon $period_start
 * @property \Carbon\Carbon $period_end
 * @property float $overall_score
 * @property float $average_rating
 * @property int $total_reviews
 * @property int $total_orders
 * @property int $completed_orders
 * @property int $cancelled_orders
 * @property float $total_revenue
 * @property float $average_order_value
 * @property float $on_time_delivery_rate
 * @property float $response_rate
 * @property float $fulfillment_rate
 * @property float $average_response_time_hours
 * @property float $average_delivery_days
 * @property int $quality_issues_count
 * @property int $returns_count
 * @property int $disputes_count
 * @property int $rfqs_received
 * @property int $quotations_submitted
 * @property int $orders_won
 * @property float $win_rate
 * @property array|null $badges
 * @property int|null $category_rank
 * @property int|null $overall_rank
 */
class SupplierPerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'period_start',
        'period_end',
        'overall_score',
        'average_rating',
        'total_reviews',
        'total_orders',
        'completed_orders',
        'cancelled_orders',
        'total_revenue',
        'average_order_value',
        'on_time_delivery_rate',
        'response_rate',
        'fulfillment_rate',
        'average_response_time_hours',
        'average_delivery_days',
        'quality_issues_count',
        'returns_count',
        'disputes_count',
        'rfqs_received',
        'quotations_submitted',
        'orders_won',
        'win_rate',
        'badges',
        'category_rank',
        'overall_rank',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'overall_score' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'response_rate' => 'decimal:2',
        'fulfillment_rate' => 'decimal:2',
        'average_response_time_hours' => 'decimal:2',
        'average_delivery_days' => 'decimal:2',
        'win_rate' => 'decimal:2',
        'badges' => 'array',
    ];

    /**
     * Get the supplier for this metric.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get performance grade (A-F).
     */
    public function getGradeAttribute(): string
    {
        return match (true) {
            $this->overall_score >= 90 => 'A',
            $this->overall_score >= 80 => 'B',
            $this->overall_score >= 70 => 'C',
            $this->overall_score >= 60 => 'D',
            default => 'F',
        };
    }

    /**
     * Get performance tier.
     */
    public function getTierAttribute(): string
    {
        return match (true) {
            $this->overall_score >= 90 => 'diamond',
            $this->overall_score >= 80 => 'platinum',
            $this->overall_score >= 70 => 'gold',
            $this->overall_score >= 60 => 'silver',
            default => 'bronze',
        };
    }

    /**
     * Check if supplier is top performer.
     */
    public function isTopPerformer(): bool
    {
        return $this->overall_score >= 90;
    }

    /**
     * Get earned badges with Arabic names.
     */
    public function getBadgeLabels(): array
    {
        if (!$this->badges) {
            return [];
        }

        $badgeMap = [
            'top_rated' => 'الأعلى تقييماً',
            'fast_delivery' => 'توصيل سريع',
            'quality_assured' => 'جودة مضمونة',
            'responsive' => 'سريع الاستجابة',
            'reliable' => 'موثوق',
            'best_value' => 'أفضل قيمة',
            'verified' => 'موثق',
            'rising_star' => 'نجم صاعد',
        ];

        return array_map(fn($badge) => $badgeMap[$badge] ?? $badge, $this->badges);
    }
}
