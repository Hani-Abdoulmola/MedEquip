<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase 3: Price alerts for buyers.
 */
class BuyerPriceAlert extends Model
{
    protected $fillable = [
        'buyer_id',
        'product_id',
        'target_price',
        'is_active',
        'triggered_at',
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
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
}
