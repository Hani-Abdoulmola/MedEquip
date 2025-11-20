<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Rfq extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'buyer_id',
        'reference_code',
        'title',
        'description',
        'deadline',
        'status',
        'is_public',
    ];

    protected $casts = [
        'deadline' => 'datetime:Y-m-d H:i',
        'is_public' => 'boolean',
    ];

    // 🔗 المشتري الذي أنشأ RFQ
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    // 💬 RFQ يمكن أن يحتوي على عدة عروض أسعار (Quotations)
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'rfq_id');
    }

    // 📦 عناصر RFQ (تفاصيل المنتجات المطلوبة)
    public function items()
    {
        return $this->hasMany(RfqItem::class, 'rfq_id');
    }

    // 📎 ملفات RFQ — مستندات أو صور عبر Spatie Media Library
    public function registerMediaCollections(): void
    {
        // مستندات الطلب (مثل ملف PDF أو Excel)
        $this->addMediaCollection('rfq_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.ms-excel'])
            ->withResponsiveImages();

        // صور مرفقة إن وجدت
        $this->addMediaCollection('rfq_images')
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
