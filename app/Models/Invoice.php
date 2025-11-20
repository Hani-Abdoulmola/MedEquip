<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Invoice extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'tax',
        'discount',
        'total_amount',
        'status',
        'payment_status',
        'created_by',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'datetime:Y-m-d H:i',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // 🔗 العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // 📎 إدارة مرفقات الفواتير عبر Spatie Media Library
    public function registerMediaCollections(): void
    {
        // ✅ ملفات PDF للفواتير الرسمية
        $this->addMediaCollection('invoice_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf'])
            ->withResponsiveImages();

        // ✅ صور الفواتير الورقية أو الإيصالات
        $this->addMediaCollection('invoice_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->withResponsiveImages();
    }

    // ⚙️ تحويلات تلقائية للصور
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }

    // 🔖 ثابتات للحالات (اختياري للتوثيق)
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ISSUED = 'issued';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PARTIAL = 'partial';

    public const PAYMENT_PAID = 'paid';
}
