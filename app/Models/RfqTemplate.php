<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RfqTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'name',
        'description',
        'category',
        'department',
        'default_deadline_days',
        'is_public',
        'is_shared',
        'use_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_shared' => 'boolean',
        'use_count' => 'integer',
        'default_deadline_days' => 'integer',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the buyer that owns this template.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    /**
     * Get the items in this template.
     */
    public function items(): HasMany
    {
        return $this->hasMany(RfqTemplateItem::class, 'template_id')->orderBy('sort_order');
    }

    /**
     * Increment use count and update last used timestamp.
     */
    public function markAsUsed(): void
    {
        $this->increment('use_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Create RFQ from this template.
     */
    public function createRfq(array $additionalData = []): Rfq
    {
        $rfq = Rfq::create(array_merge([
            'buyer_id' => $this->buyer_id,
            'created_by' => auth()->id(),
            'title' => $this->name,
            'description' => $this->description,
            'deadline' => now()->addDays($this->default_deadline_days),
            'is_public' => $this->is_public,
            'status' => 'draft',
            'reference_code' => \App\Services\ReferenceCodeService::generateUnique(
                \App\Services\ReferenceCodeService::PREFIX_RFQ,
                Rfq::class
            ),
        ], $additionalData));

        // Create RFQ items from template items
        foreach ($this->items as $templateItem) {
            RfqItem::create([
                'rfq_id' => $rfq->id,
                'product_id' => $templateItem->product_id,
                'item_name' => $templateItem->item_name,
                'specifications' => $templateItem->specifications,
                'quantity' => $templateItem->quantity,
                'unit' => $templateItem->unit,
            ]);
        }

        // Mark template as used
        $this->markAsUsed();

        return $rfq;
    }
}
