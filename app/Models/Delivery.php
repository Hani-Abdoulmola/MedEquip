<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Delivery extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'order_id',
        'supplier_id',
        'buyer_id',
        'created_by',
        'verified_by',
        'delivery_number',
        'delivery_date',
        'status',
        'delivery_location',
        'receiver_name',
        'receiver_phone',
        'notes',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'delivery_date' => 'datetime:Y-m-d H:i',
        'verified_at' => 'datetime:Y-m-d H:i',
        'is_verified' => 'boolean',
    ];

    // ======================
    // 🔗 العلاقات
    // ======================

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ======================
    // 📦 إدارة الملفات عبر Spatie Media Library
    // ======================

    public function registerMediaCollections(): void
    {
        // صور أو مستندات إثبات التسليم
        $this->addMediaCollection('delivery_proofs')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'application/pdf'])
            ->withResponsiveImages();

        // ملفات أخرى مرتبطة بعملية التسليم (اختياري)
        $this->addMediaCollection('delivery_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf'])
            ->withResponsiveImages();
    }

    // تحويلات تلقائية للصور
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }

    // ======================
    // 🧠 Accessors & Logic
    // ======================

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // ======================
    // 🔖 ثابتات الحالة
    // ======================

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_FAILED = 'failed';
}
