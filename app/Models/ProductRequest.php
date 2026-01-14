<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Product Request Model
 * 
 * Represents a supplier's request to add a new product to the canonical catalog.
 * Enforces the workflow where suppliers cannot create products directly.
 * 
 * @property int $id
 * @property int $supplier_id
 * @property int|null $existing_product_id
 * @property string $name
 * @property string|null $model
 * @property string|null $brand
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @property string|null $description
 * @property array|null $specifications
 * @property array|null $features
 * @property array|null $certifications
 * @property array|null $technical_data
 * @property string|null $installation_requirements
 * @property float|null $proposed_price
 * @property int $proposed_stock
 * @property string|null $proposed_lead_time
 * @property string|null $proposed_warranty
 * @property string $status
 * @property string|null $admin_notes
 * @property string|null $rejection_reason
 * @property int|null $reviewed_by
 * @property \Carbon\Carbon|null $reviewed_at
 * @property int|null $duplicate_of
 * @property string|null $canonical_hash
 * @property float|null $similarity_score
 */
class ProductRequest extends Model
{
    use Auditable, HasFactory, SoftDeletes, LogsActivity;

    // ==========================================
    // Status Constants
    // ==========================================
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_MERGED = 'merged';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DUPLICATE = 'duplicate';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'supplier_id',
        'existing_product_id',
        'name',
        'model',
        'brand',
        'category_id',
        'manufacturer_id',
        'description',
        'specifications',
        'features',
        'certifications',
        'technical_data',
        'installation_requirements',
        'proposed_price',
        'proposed_stock',
        'proposed_lead_time',
        'proposed_warranty',
        'status',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'duplicate_of',
        'canonical_hash',
        'similarity_score',
    ];

    protected $casts = [
        'specifications' => 'array',
        'features' => 'array',
        'certifications' => 'array',
        'technical_data' => 'array',
        'proposed_price' => 'decimal:2',
        'proposed_stock' => 'integer',
        'similarity_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the supplier who submitted this request.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the product this request was merged into (if applicable).
     */
    public function existingProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'existing_product_id');
    }

    /**
     * Get the proposed category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the proposed manufacturer.
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Get the admin who reviewed this request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the product this was marked as duplicate of.
     */
    public function duplicateProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'duplicate_of');
    }

    // ==========================================
    // Query Scopes
    // ==========================================

    /**
     * Filter pending requests.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Filter approved requests.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Filter rejected requests.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Filter by supplier.
     */
    public function scopeForSupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Filter requests that need review.
     */
    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_DUPLICATE]);
    }

    // ==========================================
    // Status Helpers
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isMerged(): bool
    {
        return $this->status === self::STATUS_MERGED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isDuplicate(): bool
    {
        return $this->status === self::STATUS_DUPLICATE;
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_DUPLICATE]);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'تمت الموافقة',
            self::STATUS_MERGED => 'تم الدمج',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_DUPLICATE => 'مكرر',
            self::STATUS_CANCELLED => 'ملغي',
            default => 'غير معروف',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_MERGED => 'blue',
            self::STATUS_REJECTED => 'red',
            self::STATUS_DUPLICATE => 'purple',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }

    // ==========================================
    // Activity Log
    // ==========================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'status',
                'admin_notes',
                'rejection_reason',
                'reviewed_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ==========================================
    // Actions
    // ==========================================

    /**
     * Approve the request and create a new product.
     */
    public function approve(User $admin, ?string $notes = null): Product
    {
        $product = Product::create([
            'created_by' => $admin->id,
            'name' => $this->name,
            'model' => $this->model,
            'brand' => $this->brand,
            'category_id' => $this->category_id,
            'manufacturer_id' => $this->manufacturer_id,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'features' => $this->features,
            'certifications' => $this->certifications,
            'technical_data' => $this->technical_data,
            'installation_requirements' => $this->installation_requirements,
            'review_status' => Product::REVIEW_APPROVED,
            'source' => 'supplier_request',
            'is_active' => true,
        ]);

        $this->update([
            'status' => self::STATUS_APPROVED,
            'existing_product_id' => $product->id,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        return $product;
    }

    /**
     * Merge with an existing product.
     */
    public function merge(User $admin, Product $existingProduct, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_MERGED,
            'existing_product_id' => $existingProduct->id,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject the request.
     */
    public function reject(User $admin, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Mark as duplicate.
     */
    public function markAsDuplicate(Product $duplicateOf, float $similarityScore): void
    {
        $this->update([
            'status' => self::STATUS_DUPLICATE,
            'duplicate_of' => $duplicateOf->id,
            'similarity_score' => $similarityScore,
        ]);
    }

    /**
     * Cancel the request (by supplier).
     */
    public function cancel(): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('لا يمكن إلغاء هذا الطلب');
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }
}

