<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class BuyerCart extends Model
{
    protected $fillable = [
        'buyer_id',
        'name',
        'template_name',
        'is_template',
        'source',
        'original_order_id',
        'is_active',
        'is_saved',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_saved' => 'boolean',
        'is_template' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set expiration for active carts (30 days)
        static::creating(function ($cart) {
            if ($cart->is_active && !$cart->expires_at) {
                $cart->expires_at = now()->addDays(30);
            }
        });
    }

    /**
     * Get the buyer that owns this cart.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    /**
     * Get the items in this cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BuyerCartItem::class, 'cart_id');
    }

    /**
     * Get the active cart for a buyer (or create one).
     */
    public static function getOrCreateActive(Buyer $buyer): self
    {
        $cart = static::where('buyer_id', $buyer->id)
            ->where('is_active', true)
            ->first();

        if (!$cart) {
            $cart = static::create([
                'buyer_id' => $buyer->id,
                'is_active' => true,
                'expires_at' => now()->addDays(30),
            ]);
        }

        // Refresh expiration if cart is about to expire
        if ($cart->expires_at && $cart->expires_at->isPast()) {
            $cart->update(['expires_at' => now()->addDays(30)]);
        }

        return $cart;
    }

    /**
     * Check if cart is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->sum('quantity');
    }
}
