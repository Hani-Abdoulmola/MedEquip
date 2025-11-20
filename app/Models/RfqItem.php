<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfqItem extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'rfq_id',
        'product_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime:Y-m-d H:i',
    ];

    // ======================
    // 🔗 Relationships
    // ======================

    /**
     * The RFQ this item belongs to
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    /**
     * The product this item references (nullable)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Quotation items that respond to this RFQ item
     */
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'rfq_item_id');
    }

    // ======================
    // 🔖 Scopes
    // ======================

    /**
     * Scope to get only approved items
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get only pending items
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    // ======================
    // 🧠 Helper Methods
    // ======================

    /**
     * Check if item is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }

    /**
     * Approve this RFQ item
     */
    public function approve(): bool
    {
        $this->is_approved = true;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Get display name (product name or custom item name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->product?->name ?? $this->item_name;
    }
}

