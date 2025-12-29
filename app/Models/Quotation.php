<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Quotation extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'reference_code',
        'total_price',
        'terms',
        'status',
        'valid_until',
        'rejection_reason',
        'updated_by',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'valid_until' => 'datetime:Y-m-d H:i',
    ];

    // 🔗 RFQ المرتبط بالعرض
    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    // 🔗 المورد الذي قدّم العرض
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // 📦 أوامر الشراء الناتجة عن قبول هذا العرض
    public function orders()
    {
        return $this->hasMany(Order::class, 'quotation_id');
    }

    // 📋 عناصر العرض (تفاصيل الأسعار لكل منتج)
    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    // 📎 مرفقات العرض (مثل ملفات PDF، صور المنتجات)
    public function registerMediaCollections(): void
    {
        // ✅ ملفات العروض (مثلاً ملفات PDF)
        $this->addMediaCollection('quotation_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'image/jpeg', 'image/png'])
            ->withResponsiveImages();

        // ✅ صور إضافية إن وُجدت
        $this->addMediaCollection('quotation_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->withResponsiveImages();
    }

    // ⚙️ تحويلات تلقائية للصور
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
