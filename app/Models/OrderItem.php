<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'quotation_item_id',
        'product_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_price',
        'lead_time',
        'warranty',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // ======================
    // 🔗 Relationships
    // ======================

    /**
     * The order this item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * The quotation item this order item is based on
     */
    public function quotationItem(): BelongsTo
    {
        return $this->belongsTo(QuotationItem::class, 'quotation_item_id');
    }

    /**
     * The product being ordered
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // ======================
    // 🔖 Status Constants
    // ======================

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_PRODUCTION = 'in_production';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    // ======================
    // 🔖 Scopes
    // ======================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    // ======================
    // 🧠 Helper Methods
    // ======================

    /**
     * Calculate total price (subtotal + tax - discount)
     * Returns decimal value (no type hint to maintain precision)
     */
    public function calculateTotalPrice()
    {
        $subtotal = $this->unit_price * $this->quantity;
        $total = $subtotal + $this->tax_amount - $this->discount_amount;
        return max(0, $total);
    }

    /**
     * Auto-calculate prices before saving
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->unit_price && $item->quantity) {
                $item->subtotal = $item->unit_price * $item->quantity;
                $item->total_price = $item->calculateTotalPrice();
            }
        });
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->product?->name ?? $this->item_name;
    }

    /**
     * Check if item is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }
}

