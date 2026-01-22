<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'product_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the template that owns this item.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(RfqTemplate::class, 'template_id');
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
