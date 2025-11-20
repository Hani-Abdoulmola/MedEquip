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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     *  علاقة Many-to-Many مع الموردين عبر الجدول الوسيط (product_supplier)
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
     *  عرض جميع عروض الموردين لهذا المنتج (للاستخدام في الواجهات)
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
     * Get the category this product belongs to
     * الفئة التي ينتمي إليها المنتج
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     *  المستخدم الذي أنشأ المنتج
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     *  المستخدم الذي عدّل المنتج
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * RFQ items that reference this product
     */
    public function rfqItems()
    {
        return $this->hasMany(RfqItem::class, 'product_id');
    }

    /**
     *  إدارة ملفات الميديا باستخدام Spatie Media Library
     */
    public function registerMediaCollections(): void
    {
        //  صور المنتج
        $this->addMediaCollection('product_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();

        //  ملفات المواصفات أو الكتالوجات
        $this->addMediaCollection('product_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf'])
            ->withResponsiveImages();
    }

    /**
     * ⚙️ إنشاء الصور المصغرة (Thumbnails) تلقائيًا
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
