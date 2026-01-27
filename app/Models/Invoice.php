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
        'acknowledged_at',
        'acknowledged_by',
        'disputed_at',
        'dispute_reason',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'datetime:Y-m-d H:i',
        'acknowledged_at' => 'datetime:Y-m-d H:i',
        'disputed_at' => 'datetime:Y-m-d H:i',
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

    public function acknowledger()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
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

    // ======================
    // 🧮 Price Calculation Methods
    // ======================

    /**
     * Calculate total amount from subtotal, tax, and discount
     * Formula: total_amount = subtotal + tax - discount
     * 
     * @return float
     */
    public function calculateTotalAmount(): float
    {
        $subtotal = (float) ($this->subtotal ?? 0);
        $tax = (float) ($this->tax ?? 0);
        $discount = (float) ($this->discount ?? 0);
        
        return max(0, $subtotal + $tax - $discount);
    }

    /**
     * Check if invoice can transition to a new status
     * 
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            self::STATUS_DRAFT => [self::STATUS_ISSUED, self::STATUS_CANCELLED],
            self::STATUS_ISSUED => [self::STATUS_APPROVED, self::STATUS_CANCELLED],
            self::STATUS_APPROVED => [self::STATUS_CANCELLED], // May need payment check
            self::STATUS_CANCELLED => [], // Terminal state
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    /**
     * Recalculate invoice amounts from order items
     */
    public function recalculateFromOrder(): void
    {
        if (!$this->order) {
            return;
        }

        $this->order->load('items');

        if ($this->order->items->isEmpty()) {
            return;
        }

        // Calculate from order items
        $this->subtotal = $this->order->items->sum(function ($item) {
            return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
        });

        $this->tax = $this->order->items->sum('tax_amount') ?? 0;
        $this->discount = $this->order->items->sum('discount_amount') ?? 0;
        $this->total_amount = $this->calculateTotalAmount();
    }

    /**
     * Apply discount to invoice
     * 
     * @param float $amount
     * @param string $type 'fixed' or 'percentage'
     */
    public function applyDiscount(float $amount, string $type = 'fixed'): void
    {
        if ($type === 'percentage') {
            // Percentage discount
            $this->discount = ($this->subtotal * $amount) / 100;
        } else {
            // Fixed amount discount
            $this->discount = min($amount, $this->subtotal); // Cannot exceed subtotal
        }

        $this->total_amount = $this->calculateTotalAmount();
    }

    /**
     * Apply tax rate to invoice
     * 
     * @param float $rate Tax rate as percentage (e.g., 15 for 15%)
     */
    public function applyTax(float $rate): void
    {
        $this->tax = ($this->subtotal * $rate) / 100;
        $this->total_amount = $this->calculateTotalAmount();
    }

    /**
     * Lock pricing - prevent further changes after approval
     */
    public function lockPricing(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get total amount paid
     */
    public function getTotalPaid(): float
    {
        return (float) $this->payments()
            ->where('status', \App\Models\Payment::STATUS_COMPLETED)
            ->sum('amount');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalance(): float
    {
        return max(0, $this->total_amount - $this->getTotalPaid());
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->getTotalPaid() >= $this->total_amount;
    }

    /**
     * Refresh payment status based on payments
     */
    public function refreshPaymentStatus(): void
    {
        $totalPaid = $this->getTotalPaid();

        if ($totalPaid >= $this->total_amount) {
            $this->payment_status = self::PAYMENT_PAID;
        } elseif ($totalPaid > 0) {
            $this->payment_status = self::PAYMENT_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_UNPAID;
        }

        $this->save();
    }

    /**
     * Auto-calculate total_amount before saving
     */
    protected static function booted()
    {
        static::saving(function ($invoice) {
            // Auto-calculate total_amount if subtotal is set
            if ($invoice->subtotal !== null) {
                $invoice->total_amount = $invoice->calculateTotalAmount();
            }
        });
    }
}
