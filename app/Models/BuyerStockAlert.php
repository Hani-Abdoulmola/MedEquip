<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase 3: Stock alerts for buyers.
 */
class BuyerStockAlert extends Model
{
    protected $fillable = [
        'buyer_id',
        'product_id',
        'supplier_id',
        'is_active',
        'triggered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'triggered_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
