<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Product Model
 * 
 * Represents medical equipment products with manufacturer, category,
 * and supplier relationships.
 * 
 * @property int $id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string $name
 * @property string|null $model
 * @property string|null $brand
 * @property int|null $manufacturer_id
 * @property int|null $category_id
 * @property string|null $description
 * @property bool $is_active
 * @property string $review_status (pending, approved, needs_update, rejected)
 * @property string|null $review_notes
 * @property string|null $rejection_reason
 * @property array|null $specifications
 * @property array|null $features
 * @property array|null $technical_data
 * @property array|null $certifications
 * @property string|null $installation_requirements
 * 
 * @property-read User|null $creator
 * @property-read User|null $updater
 * @property-read ProductCategory|null $category
 * @property-read Manufacturer|null $manufacturer
 * @property-read \Illuminate\Database\Eloquent\Collection|Supplier[] $suppliers
 * @property-read \Illuminate\Database\Eloquent\Collection|RfqItem[] $rfqItems
 */
class Product extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * Review status constants.
     */
    const REVIEW_PENDING = 'pending';
    const REVIEW_APPROVED = 'approved';
    const REVIEW_NEEDS_UPDATE = 'needs_update';
    const REVIEW_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'model',
        'brand',
        'manufacturer_id',
        'category_id',
        'description',
        'is_active',
        'review_status',
        'review_notes',
        'rejection_reason',
        'specifications',
        'features',
        'technical_data',
        'certifications',
        'installation_requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'specifications' => 'array',
        'features' => 'array',
        'technical_data' => 'array',
        'certifications' => 'array',
    ];

    // ================================
    // Relationships
    // ================================

    /**
     * Get the suppliers offering this product (many-to-many).
     *
     * @return BelongsToMany
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
            ->withPivot([
                'price',
                'stock_quantity',
                'lead_time',
                'warranty',
                'status',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get active supplier offers for this product.
     *
     * @return BelongsToMany
     */
    public function offers(): BelongsToMany
    {
        return $this->suppliers()
            ->wherePivot('status', 'available')
            ->select([
                'suppliers.id',
                'suppliers.company_name',
                'product_supplier.price',
                'product_supplier.stock_quantity',
                'product_supplier.lead_time',
                'product_supplier.warranty',
                'product_supplier.status'
            ]);
    }

    /**
     * Get the product category.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the manufacturer.
     *
     * @return BelongsTo
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Get the user who created this product.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this product.
     *
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all RFQ items for this product.
     *
     * @return HasMany
     */
    public function rfqItems(): HasMany
    {
        return $this->hasMany(RfqItem::class, 'product_id');
    }

    // ================================
    // Query Scopes
    // ================================

    /**
     * Scope to filter only active products.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by review status.
     *
     * @param Builder $query
     * @param string $status
     * @return Builder
     */
    public function scopeReviewStatus(Builder $query, string $status): Builder
    {
        return $query->where('review_status', $status);
    }

    /**
     * Scope to filter approved products only.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('review_status', self::REVIEW_APPROVED);
    }

    /**
     * Scope to filter pending review products.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('review_status', self::REVIEW_PENDING);
    }

    /**
     * Scope to filter by category.
     *
     * @param Builder $query
     * @param int $categoryId
     * @return Builder
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by manufacturer.
     *
     * @param Builder $query
     * @param int $manufacturerId
     * @return Builder
     */
    public function scopeByManufacturer(Builder $query, int $manufacturerId): Builder
    {
        return $query->where('manufacturer_id', $manufacturerId);
    }

    // ================================
    // Media Collections
    // ================================

    /**
     * Register media collections for the product.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();

        $this->addMediaCollection('product_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * Register media conversions for product images.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->nonQueued();
    }

    // ================================
    // Helper Methods
    // ================================

    /**
     * Check if product is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->review_status === self::REVIEW_APPROVED;
    }

    /**
     * Check if product is pending review.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->review_status === self::REVIEW_PENDING;
    }

    /**
     * Check if product needs update.
     *
     * @return bool
     */
    public function needsUpdate(): bool
    {
        return $this->review_status === self::REVIEW_NEEDS_UPDATE;
    }

    /**
     * Check if product is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->review_status === self::REVIEW_REJECTED;
    }

    /**
     * Get the review status label in Arabic.
     *
     * @return string
     */
    public function getReviewStatusLabelAttribute(): string
    {
        return match($this->review_status) {
            self::REVIEW_PENDING => 'قيد المراجعة',
            self::REVIEW_APPROVED => 'معتمد',
            self::REVIEW_NEEDS_UPDATE => 'يحتاج تعديل',
            self::REVIEW_REJECTED => 'مرفوض',
            default => 'غير محدد',
        };
    }
}
