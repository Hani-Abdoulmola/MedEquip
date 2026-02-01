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

    // 🔖 Payment Status Constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

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
            ->acceptsMimeTypes(['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'application/pdf'])
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

    // ======================
    // 🏷️ Helper Methods for Labels
    // ======================

    /**
     * Get payment status label in Arabic
     */
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_FAILED => 'فاشلة',
            self::STATUS_REFUNDED => 'مستردة',
            default => $status,
        };
    }

    /**
     * Get payment status CSS classes
     */
    public static function getStatusClasses(string $status): string
    {
        return match($status) {
            self::STATUS_PENDING => 'bg-medical-yellow-100 text-medical-yellow-700',
            self::STATUS_COMPLETED => 'bg-medical-green-100 text-medical-green-700',
            self::STATUS_FAILED => 'bg-medical-red-100 text-medical-red-700',
            self::STATUS_REFUNDED => 'bg-medical-blue-100 text-medical-blue-700',
            default => 'bg-medical-gray-100 text-medical-gray-700',
        };
    }

    /**
     * Get all status options for select dropdown
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => self::getStatusLabel(self::STATUS_PENDING),
            self::STATUS_COMPLETED => self::getStatusLabel(self::STATUS_COMPLETED),
            self::STATUS_FAILED => self::getStatusLabel(self::STATUS_FAILED),
            self::STATUS_REFUNDED => self::getStatusLabel(self::STATUS_REFUNDED),
        ];
    }

    /**
     * Get payment method label in Arabic
     */
    public static function getMethodLabel(string $method): string
    {
        return match($method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمانية',
            'paypal' => 'PayPal',
            'other' => 'أخرى',
            default => $method,
        };
    }

    /**
     * Get all method options for select dropdown
     */
    public static function getMethodOptions(): array
    {
        return [
            'cash' => self::getMethodLabel('cash'),
            'bank_transfer' => self::getMethodLabel('bank_transfer'),
            'credit_card' => self::getMethodLabel('credit_card'),
            'paypal' => self::getMethodLabel('paypal'),
            'other' => self::getMethodLabel('other'),
        ];
    }

    /**
     * Get currency label in Arabic
     */
    public static function getCurrencyLabel(string $currency): string
    {
        return match($currency) {
            self::CURRENCY_LYD => 'دينار ليبي',
            self::CURRENCY_USD => 'دولار أمريكي',
            self::CURRENCY_EUR => 'يورو',
            default => $currency,
        };
    }

    /**
     * Get all currency options for select dropdown
     */
    public static function getCurrencyOptions(): array
    {
        return [
            self::CURRENCY_LYD => self::getCurrencyLabel(self::CURRENCY_LYD),
            self::CURRENCY_USD => self::getCurrencyLabel(self::CURRENCY_USD),
            self::CURRENCY_EUR => self::getCurrencyLabel(self::CURRENCY_EUR),
        ];
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol(string $currency): string
    {
        return match($currency) {
            self::CURRENCY_LYD => 'د.ل',
            self::CURRENCY_USD => '$',
            self::CURRENCY_EUR => '€',
            default => $currency,
        };
    }
}
