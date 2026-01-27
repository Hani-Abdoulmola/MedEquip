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
        'created_by',
        'updated_by',
        'reference_code',
        'title',
        'description',
        'deadline',
        'published_at',
        'closed_at',
        'awarded_at',
        'cancelled_at',
        'awarded_quotation_id',
        'status',
        'is_public',
    ];

    protected $casts = [
        'deadline' => 'datetime:Y-m-d H:i',
        'published_at' => 'datetime:Y-m-d H:i',
        'closed_at' => 'datetime:Y-m-d H:i',
        'awarded_at' => 'datetime:Y-m-d H:i',
        'cancelled_at' => 'datetime:Y-m-d H:i',
        'is_public' => 'boolean',
    ];

    // 🔗 المشتري الذي أنشأ RFQ
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    // 👤 المستخدم الذي أنشأ الطلب
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 👤 المستخدم الذي عدّل الطلب آخر مرة
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // 💬 RFQ يمكن أن يحتوي على عدة عروض أسعار (Quotations)
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'rfq_id');
    }

    // 🏆 The awarded quotation (winning bid)
    public function awardedQuotation()
    {
        return $this->belongsTo(Quotation::class, 'awarded_quotation_id');
    }

    // 📦 عناصر RFQ (تفاصيل المنتجات المطلوبة)
    public function items()
    {
        return $this->hasMany(RfqItem::class, 'rfq_id');
    }

    // 🏭 الموردون المعينون لهذا الطلب
    public function assignedSuppliers()
    {
        return $this->belongsToMany(Supplier::class, 'rfq_supplier')
            ->withPivot(['status', 'invited_at', 'viewed_at', 'notes'])
            ->withTimestamps();
    }

    // ✅ Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeAssignedTo($query, $supplierId)
    {
        return $query->whereHas('assignedSuppliers', function ($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        });
    }

    public function scopeAvailableFor($query, $supplierId)
    {
        return $query->where('status', 'open') // Only show open RFQs
            ->where(function ($q) use ($supplierId) {
                // Public RFQs (visible to all verified suppliers)
                $q->where('is_public', true)
                  // OR assigned to this supplier
                  ->orWhereHas('assignedSuppliers', fn($sub) => $sub->where('suppliers.id', $supplierId))
                  // OR has already submitted a quotation
                  ->orWhereHas('quotations', fn($sub) => $sub->where('supplier_id', $supplierId));
            });
    }

    // 🔍 Check if supplier is assigned
    public function isAssignedTo($supplierId): bool
    {
        return $this->assignedSuppliers()->where('suppliers.id', $supplierId)->exists();
    }

    // 🔍 Check if supplier has quoted
    public function hasQuotationFrom($supplierId): bool
    {
        return $this->quotations()->where('supplier_id', $supplierId)->exists();
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
