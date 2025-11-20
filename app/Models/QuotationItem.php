<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'rfq_item_id',
        'product_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'lead_time',
        'warranty',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // ======================
    // 🔗 Relationships
    // ======================

    /**
     * The quotation this item belongs to
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    /**
     * The RFQ item this quotation item responds to
     */
    public function rfqItem(): BelongsTo
    {
        return $this->belongsTo(RfqItem::class, 'rfq_item_id');
    }

    /**
     * The product being quoted
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Order items created from this quotation item
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'quotation_item_id');
    }

    // ======================
    // 🧠 Helper Methods
    // ======================

    /**
     * Calculate total price from unit price and quantity
     * Returns decimal value (no type hint to maintain precision)
     */
    public function calculateTotalPrice()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Auto-calculate total price before saving
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->unit_price && $item->quantity) {
                $item->total_price = $item->calculateTotalPrice();
            }
        });
    }

    /**
     * Get display name (product name or custom item name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->product?->name ?? $this->item_name;
    }
}

