<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Delivery Tracking Model
 *
 * Real-time tracking of delivery status and location.
 */
class DeliveryTracking extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'delivery_id',
        'order_id',
        'status',
        'current_location',
        'latitude',
        'longitude',
        'carrier_name',
        'tracking_number',
        'driver_name',
        'driver_phone',
        'vehicle_info',
        'estimated_delivery_at',
        'actual_delivery_at',
        'last_updated_at',
        'delivery_instructions',
        'delivery_address',
        'delivery_city',
        'notifications_sent',
        'events',
        'signature_image',
        'delivery_photos',
        'delivery_notes',
        'received_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'estimated_delivery_at' => 'datetime',
        'actual_delivery_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'notifications_sent' => 'array',
        'events' => 'array',
        'delivery_photos' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
    const STATUS_RETURNED = 'returned';

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Update tracking status with event log.
     */
    public function updateStatus(string $status, ?string $location = null, ?string $notes = null): bool
    {
        $event = [
            'status' => $status,
            'location' => $location,
            'notes' => $notes,
            'timestamp' => now()->toIso8601String(),
        ];

        $events = $this->events ?? [];
        $events[] = $event;

        return $this->update([
            'status' => $status,
            'current_location' => $location ?? $this->current_location,
            'events' => $events,
            'last_updated_at' => now(),
        ]);
    }

    /**
     * Mark as delivered with proof.
     */
    public function markDelivered(string $receivedBy, ?string $signature = null, ?array $photos = null, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_DELIVERED,
            'actual_delivery_at' => now(),
            'received_by' => $receivedBy,
            'signature_image' => $signature,
            'delivery_photos' => $photos,
            'delivery_notes' => $notes,
            'last_updated_at' => now(),
        ]);
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_CONFIRMED => 'مؤكد',
            self::STATUS_PREPARING => 'قيد التحضير',
            self::STATUS_SHIPPED => 'تم الشحن',
            self::STATUS_IN_TRANSIT => 'في الطريق',
            self::STATUS_OUT_FOR_DELIVERY => 'خارج للتوصيل',
            self::STATUS_DELIVERED => 'تم التوصيل',
            self::STATUS_FAILED => 'فشل',
            self::STATUS_RETURNED => 'تم الإرجاع',
            default => 'غير معروف',
        };
    }

    /**
     * Check if delivery is late.
     */
    public function isLate(): bool
    {
        return $this->estimated_delivery_at
            && now()->gt($this->estimated_delivery_at)
            && !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_FAILED, self::STATUS_RETURNED]);
    }

    /**
     * Get delivery progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_PENDING => 0,
            self::STATUS_CONFIRMED => 10,
            self::STATUS_PREPARING => 25,
            self::STATUS_SHIPPED => 40,
            self::STATUS_IN_TRANSIT => 60,
            self::STATUS_OUT_FOR_DELIVERY => 80,
            self::STATUS_DELIVERED => 100,
            default => 0,
        };
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'current_location', 'actual_delivery_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
