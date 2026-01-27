<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerCartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'specifications',
        'unit',
        'supplier_id',
        'max_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'max_price' => 'decimal:2',
    ];

    /**
     * Get the cart that owns this item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(BuyerCart::class, 'cart_id');
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the preferred supplier for this item.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
