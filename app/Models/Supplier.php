<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Supplier extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'commercial_register',
        'tax_number',
        // Removed: 'verification_document' - use Media Library 'verification_documents' collection
        'country',
        'city',
        'address',
        'contact_email',
        'contact_phone',
        'is_verified',
        'verified_at',
        'is_active',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime:Y-m-d H:i',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     *  المورد مرتبط بحساب مستخدم (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *  علاقة المورد بالمنتجات عبر الجدول الوسيط (Product ↔ Supplier)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
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
     *  العروض الخاصة بالمورد (لعرض منتجاته وسعرها ومخزونها)
     */
    public function offers()
    {
        return $this->products()
            ->select(
                'products.id',
                'products.name',
                'product_supplier.price',
                'product_supplier.stock_quantity',
                'product_supplier.lead_time',
                'product_supplier.warranty',
                'product_supplier.status'
            );
    }

    /**
     *  عروض الأسعار (RFQs / Quotations)
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'supplier_id');
    }

    /**
     *  عمليات التسليم الخاصة بالمورد
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'supplier_id');
    }

    /**
     *  إدارة الميديا عبر Spatie Media Library
     */
    public function registerMediaCollections(): void
    {
        //  وثائق التحقق الرسمية (PDF أو صور)
        $this->addMediaCollection('verification_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->singleFile();

        //  صور عامة (شعار الشركة أو صور المصنع)
        $this->addMediaCollection('supplier_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->withResponsiveImages();
    }

    /**
     *  إنشاء الصور المصغرة تلقائيًا
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
