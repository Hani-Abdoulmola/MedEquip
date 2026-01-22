<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Product Review Model
 *
 * Handles reviews and ratings for products from buyers.
 */
class ProductReview extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'order_id',
        'overall_rating',
        'quality_rating',
        'value_rating',
        'accuracy_rating',
        'title',
        'review',
        'pros',
        'cons',
        'would_recommend',
        'status',
        'is_verified_purchase',
        'photos',
        'moderated_by',
        'moderated_at',
        'admin_notes',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'quality_rating' => 'integer',
        'value_rating' => 'integer',
        'accuracy_rating' => 'integer',
        'would_recommend' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'photos' => 'array',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->quality_rating,
            $this->value_rating,
            $this->accuracy_rating,
        ]);

        return empty($ratings) ? 0 : round(array_sum($ratings) / count($ratings), 1);
    }

    public function approve(int $adminId, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'moderated_by' => $adminId,
            'moderated_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject(int $adminId, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'moderated_by' => $adminId,
            'moderated_at' => now(),
            'admin_notes' => $reason,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['overall_rating', 'title', 'review', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
