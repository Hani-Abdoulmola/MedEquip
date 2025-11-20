<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplier extends Model
{
    protected $table = 'product_supplier';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'price',
        'stock_quantity',
        'lead_time',
        'warranty',
        'status',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    // 🔗 العلاقات
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // ⚙️ Scopes مساعدة
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeActiveSupplier($query)
    {
        return $query->whereHas('supplier', fn ($q) => $q->where('is_verified', true));
    }
}
