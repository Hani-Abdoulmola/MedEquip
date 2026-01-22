<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Delivery Review Model
 *
 * Handles reviews and ratings for deliveries from buyers.
 */
class DeliveryReview extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'delivery_id',
        'order_id',
        'buyer_id',
        'supplier_id',
        'overall_rating',
        'timeliness_rating',
        'condition_rating',
        'packaging_rating',
        'professionalism_rating',
        'title',
        'review',
        'issues',
        'status',
        'is_verified_delivery',
        'photos',
        'moderated_by',
        'moderated_at',
        'admin_notes',
        'helpful_count',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'condition_rating' => 'integer',
        'packaging_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'is_verified_delivery' => 'boolean',
        'photos' => 'array',
        'helpful_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->timeliness_rating,
            $this->condition_rating,
            $this->packaging_rating,
            $this->professionalism_rating,
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
