<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BuyerFavorite Model
 * 
 * Represents a buyer's favorite product.
 * This is a pivot model for the many-to-many relationship between Buyer and Product.
 */
class BuyerFavorite extends Model
{
    protected $table = 'buyer_favorites';

    protected $fillable = [
        'buyer_id',
        'product_id',
    ];

    /**
     * Get the buyer that owns this favorite.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the product that is favorited.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

