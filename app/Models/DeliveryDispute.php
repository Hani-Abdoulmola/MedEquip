<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Delivery Dispute Model
 *
 * Handles disputes and issues related to deliveries.
 */
class DeliveryDispute extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'delivery_id',
        'order_id',
        'buyer_id',
        'supplier_id',
        'dispute_number',
        'type',
        'title',
        'description',
        'photos',
        'documents',
        'status',
        'resolution',
        'resolution_details',
        'refund_amount',
        'resolved_at',
        'closed_at',
        'assigned_to',
        'resolved_by',
        'messages',
        'admin_notes',
        'priority',
    ];

    protected $casts = [
        'photos' => 'array',
        'documents' => 'array',
        'messages' => 'array',
        'refund_amount' => 'decimal:2',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Type constants
    const TYPE_NOT_DELIVERED = 'not_delivered';
    const TYPE_LATE_DELIVERY = 'late_delivery';
    const TYPE_DAMAGED_PRODUCTS = 'damaged_products';
    const TYPE_WRONG_PRODUCTS = 'wrong_products';
    const TYPE_MISSING_ITEMS = 'missing_items';
    const TYPE_QUALITY_ISSUE = 'quality_issue';
    const TYPE_OTHER = 'other';

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_INVESTIGATING = 'investigating';
    const STATUS_WAITING_SUPPLIER = 'waiting_supplier';
    const STATUS_WAITING_BUYER = 'waiting_buyer';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_ESCALATED = 'escalated';

    // Resolution constants
    const RESOLUTION_REFUND = 'refund';
    const RESOLUTION_REPLACEMENT = 'replacement';
    const RESOLUTION_PARTIAL_REFUND = 'partial_refund';
    const RESOLUTION_REDELIVERY = 'redelivery';
    const RESOLUTION_NO_ACTION = 'no_action';
    const RESOLUTION_OTHER = 'other';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dispute) {
            if (!$dispute->dispute_number) {
                $dispute->dispute_number = 'DISP-' . strtoupper(Str::random(8));
            }
        });
    }

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

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Add message to dispute conversation.
     */
    public function addMessage(string $from, string $message, ?int $userId = null): bool
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'from' => $from,
            'user_id' => $userId,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        return $this->update(['messages' => $messages]);
    }

    /**
     * Resolve dispute.
     */
    public function resolve(string $resolution, ?string $details = null, ?float $refundAmount = null, int $resolvedBy): bool
    {
        return $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution' => $resolution,
            'resolution_details' => $details,
            'refund_amount' => $refundAmount,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Close dispute.
     */
    public function close(?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CLOSED,
            'admin_notes' => $notes,
            'closed_at' => now(),
        ]);
    }

    /**
     * Get type label in Arabic.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NOT_DELIVERED => 'لم يتم التوصيل',
            self::TYPE_LATE_DELIVERY => 'تأخر في التوصيل',
            self::TYPE_DAMAGED_PRODUCTS => 'منتجات تالفة',
            self::TYPE_WRONG_PRODUCTS => 'منتجات خاطئة',
            self::TYPE_MISSING_ITEMS => 'عناصر مفقودة',
            self::TYPE_QUALITY_ISSUE => 'مشكلة في الجودة',
            self::TYPE_OTHER => 'أخرى',
            default => 'غير معروف',
        };
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'مفتوح',
            self::STATUS_INVESTIGATING => 'قيد التحقيق',
            self::STATUS_WAITING_SUPPLIER => 'في انتظار المورد',
            self::STATUS_WAITING_BUYER => 'في انتظار المشتري',
            self::STATUS_RESOLVED => 'تم الحل',
            self::STATUS_CLOSED => 'مغلق',
            self::STATUS_ESCALATED => 'تم التصعيد',
            default => 'غير معروف',
        };
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'resolution', 'refund_amount', 'priority'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
