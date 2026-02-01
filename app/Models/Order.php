<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Order extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'buyer_id',
        'supplier_id',
        'created_by',
        'order_number',
        'order_date',
        'status',
        'total_amount',
        'currency',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'datetime:Y-m-d H:i',
        'total_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'currency' => self::CURRENCY_LYD,
    ];

    // 🔖 Currency Constants
    public const CURRENCY_LYD = 'LYD';  // Libyan Dinar (default)
    public const CURRENCY_USD = 'USD';  // US Dollar
    public const CURRENCY_EUR = 'EUR';  // Euro

    // 🔗 العلاقات الأساسية
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'order_id');
    }

    // 👤 المستخدم الذي أنشأ الطلب
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🧾 مرفقات الأوامر (مثل ملفات PDF أو إثباتات)
    public function registerMediaCollections(): void
    {
        // ✅ ملفات أوامر الشراء بصيغة PDF
        $this->addMediaCollection('order_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf'])
            ->withResponsiveImages();

        // ✅ صور مرتبطة بأمر الشراء (توقيع، فاتورة ورقية، إلخ)
        $this->addMediaCollection('order_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png'])
            ->withResponsiveImages();
    }

    // ⚙️ التحويلات التلقائية للصور
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }

    // 🔖 خيارات الحالة (اختياري - توثيق داخلي)
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';
}
