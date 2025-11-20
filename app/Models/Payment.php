<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Payment extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'order_id',
        'buyer_id',
        'supplier_id',
        'processed_by',
        'payment_reference',
        'amount',
        'currency',
        'method',
        'transaction_id',
        'status',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime:Y-m-d H:i',
        'amount' => 'decimal:2',
    ];

    protected $attributes = [
        'currency' => self::CURRENCY_LYD,  // Libyan Dinar (default for Libya market)
    ];

    // 🔖 Currency Constants
    public const CURRENCY_LYD = 'LYD';  // Libyan Dinar (default)
    public const CURRENCY_USD = 'USD';  // US Dollar
    public const CURRENCY_EUR = 'EUR';  // Euro

    /**
     * Auto-sync buyer_id and supplier_id from order when payment is created
     * This maintains denormalized data integrity for reporting performance
     */
    protected static function booted()
    {
        static::creating(function ($payment) {
            if ($payment->order_id && !$payment->buyer_id) {
                $order = $payment->order;
                $payment->buyer_id = $order->buyer_id;
                $payment->supplier_id = $order->supplier_id;
            }
        });

        static::updating(function ($payment) {
            if ($payment->isDirty('order_id') && $payment->order_id) {
                $order = $payment->order;
                $payment->buyer_id = $order->buyer_id;
                $payment->supplier_id = $order->supplier_id;
            }
        });
    }

    // 🔗 العلاقات
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // 📎 ملفات الدفع (مثل إيصالات الدفع أو صور التحويل البنكي)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_receipts')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->withResponsiveImages();
    }

    // ⚙️ توليد صور مصغرة تلقائيًا عند رفع الإيصالات
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200);

        $this->addMediaConversion('preview')
            ->width(600)
            ->height(400);
    }
}
