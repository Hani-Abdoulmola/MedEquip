<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Supplier Review Model
 *
 * Handles reviews and ratings for suppliers from buyers.
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $buyer_id
 * @property int|null $order_id
 * @property int $overall_rating
 * @property int|null $quality_rating
 * @property int|null $communication_rating
 * @property int|null $delivery_rating
 * @property int|null $value_rating
 * @property string|null $title
 * @property string|null $review
 * @property string|null $pros
 * @property string|null $cons
 * @property bool $would_recommend
 * @property string $status
 * @property string|null $admin_notes
 * @property int|null $moderated_by
 * @property \Carbon\Carbon|null $moderated_at
 * @property bool $is_verified_purchase
 * @property int $helpful_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class SupplierReview extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'supplier_id',
        'buyer_id',
        'order_id',
        'overall_rating',
        'quality_rating',
        'communication_rating',
        'delivery_rating',
        'value_rating',
        'title',
        'review',
        'pros',
        'cons',
        'would_recommend',
        'status',
        'admin_notes',
        'moderated_by',
        'moderated_at',
        'is_verified_purchase',
        'helpful_count',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'quality_rating' => 'integer',
        'communication_rating' => 'integer',
        'delivery_rating' => 'integer',
        'value_rating' => 'integer',
        'would_recommend' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the supplier being reviewed.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the buyer who wrote the review.
     */
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the associated order (if any).
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin who moderated this review.
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Scope for approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for verified purchases.
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Get average rating across all dimensions.
     */
    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->quality_rating,
            $this->communication_rating,
            $this->delivery_rating,
            $this->value_rating,
        ]);

        if (empty($ratings)) {
            return 0;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    /**
     * Get the status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            default => 'غير معروف',
        };
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'overall_rating',
                'title',
                'review',
                'status',
                'admin_notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Check if review can be edited by the buyer.
     */
    public function canBeEdited(): bool
    {
        // Can only edit pending reviews or within 7 days
        return $this->status === self::STATUS_PENDING
            || $this->created_at->diffInDays(now()) <= 7;
    }

    /**
     * Approve the review.
     */
    public function approve(int $adminId, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'moderated_by' => $adminId,
            'moderated_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject the review.
     */
    public function reject(int $adminId, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'moderated_by' => $adminId,
            'moderated_at' => now(),
            'admin_notes' => $reason,
        ]);
    }
}

