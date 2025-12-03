<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'model',
        'brand',
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

    protected $casts = [
        'is_active' => 'boolean',

        'specifications' => 'array',
        'features' => 'array',
        'technical_data' => 'array',
        'certifications' => 'array',
    ];

    /**
     * علاقة Many-to-Many مع الموردين
     */
    public function suppliers()
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
     * عروض الموردين لهذا المنتج
     */
    public function offers()
    {
        return $this->suppliers()
            ->select(
                'suppliers.id',
                'suppliers.company_name',
                'product_supplier.price',
                'product_supplier.stock_quantity',
                'product_supplier.lead_time',
                'product_supplier.warranty',
                'product_supplier.status'
            );
    }

    /**
     * الفئة
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * منشئ المنتج
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * آخر من عدل المنتج
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * RFQ items
     */
    public function rfqItems()
    {
        return $this->hasMany(RfqItem::class, 'product_id');
    }

    /**
     * Media Collections
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
     * Media Conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }
}
