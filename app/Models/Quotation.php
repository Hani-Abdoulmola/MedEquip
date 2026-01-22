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

    // 📊 Scoring System for Quotation Comparison
    
    /**
     * Calculate overall quotation score (0-100).
     * Higher is better.
     */
    public function calculateScore(array $allQuotations = []): float
    {
        if (empty($allQuotations)) {
            $allQuotations = Quotation::where('rfq_id', $this->rfq_id)
                ->where('status', '!=', 'rejected')
                ->get()
                ->toArray();
        }

        // Weight factors
        $weights = [
            'price' => 0.40,      // 40% weight
            'lead_time' => 0.20,  // 20% weight
            'supplier' => 0.20,   // 20% weight
            'stock' => 0.10,      // 10% weight
            'validity' => 0.10,   // 10% weight
        ];

        $scores = [
            'price' => $this->calculatePriceScore($allQuotations),
            'lead_time' => $this->calculateLeadTimeScore(),
            'supplier' => $this->calculateSupplierScore(),
            'stock' => $this->calculateStockScore(),
            'validity' => $this->calculateValidityScore(),
        ];

        // Calculate weighted total
        $totalScore = 0;
        foreach ($scores as $key => $score) {
            $totalScore += $score * $weights[$key];
        }

        return round($totalScore, 2);
    }

    /**
     * Calculate price score (lower price = higher score).
     * Returns 0-100.
     */
    public function calculatePriceScore(array $allQuotations = []): float
    {
        if (empty($allQuotations)) {
            $allQuotations = Quotation::where('rfq_id', $this->rfq_id)
                ->where('status', '!=', 'rejected')
                ->pluck('total_price')
                ->toArray();
        }

        $prices = is_array($allQuotations) && isset($allQuotations[0]['total_price']) 
            ? array_column($allQuotations, 'total_price')
            : $allQuotations;

        $minPrice = min($prices);
        $maxPrice = max($prices);

        if ($maxPrice == $minPrice) {
            return 100;
        }

        // Inverse scoring: lower price gets higher score
        return round((($maxPrice - $this->total_price) / ($maxPrice - $minPrice)) * 100, 2);
    }

    /**
     * Calculate lead time score based on average lead time across items.
     * Shorter lead time = higher score.
     */
    public function calculateLeadTimeScore(): float
    {
        $this->loadMissing('items.rfqItem.product.suppliers');
        
        $totalLeadTime = 0;
        $itemCount = 0;

        foreach ($this->items as $item) {
            if ($item->rfqItem && $item->rfqItem->product) {
                // Get lead time from product_supplier pivot
                $productSupplier = $item->rfqItem->product->suppliers()
                    ->where('suppliers.id', $this->supplier_id)
                    ->first();
                
                if ($productSupplier && isset($productSupplier->pivot->lead_time)) {
                    $totalLeadTime += $productSupplier->pivot->lead_time;
                    $itemCount++;
                }
            }
        }

        if ($itemCount === 0) {
            return 50; // Neutral score if no lead time data
        }

        $avgLeadTime = $totalLeadTime / $itemCount;

        // Score based on lead time ranges
        // 1-7 days = 100, 8-14 = 80, 15-30 = 60, 31-60 = 40, 60+ = 20
        return match(true) {
            $avgLeadTime <= 7 => 100,
            $avgLeadTime <= 14 => 80,
            $avgLeadTime <= 30 => 60,
            $avgLeadTime <= 60 => 40,
            default => 20,
        };
    }

    /**
     * Calculate supplier score based on verification, orders, etc.
     */
    public function calculateSupplierScore(): float
    {
        $this->loadMissing('supplier');
        
        if (!$this->supplier) {
            return 0;
        }

        $score = 0;

        // Verified suppliers get +50 points
        if ($this->supplier->is_verified) {
            $score += 50;
        }

        // Active suppliers get +10 points
        if ($this->supplier->is_active) {
            $score += 10;
        }

        // Score based on completed orders (future enhancement when ratings are added)
        $completedOrders = Order::where('supplier_id', $this->supplier_id)
            ->where('status', 'delivered')
            ->count();

        // 0 orders = 0, 1-5 = 10, 6-20 = 20, 21-50 = 30, 51+ = 40
        $orderScore = match(true) {
            $completedOrders === 0 => 0,
            $completedOrders <= 5 => 10,
            $completedOrders <= 20 => 20,
            $completedOrders <= 50 => 30,
            default => 40,
        };

        $score += $orderScore;

        return min(100, $score);
    }

    /**
     * Calculate stock availability score.
     */
    public function calculateStockScore(): float
    {
        $this->loadMissing('items.rfqItem.product.suppliers');
        
        $totalItems = $this->items->count();
        $inStockItems = 0;

        foreach ($this->items as $item) {
            if ($item->rfqItem && $item->rfqItem->product) {
                // Get stock from product_supplier pivot
                $productSupplier = $item->rfqItem->product->suppliers()
                    ->where('suppliers.id', $this->supplier_id)
                    ->first();
                
                if ($productSupplier && isset($productSupplier->pivot->stock_quantity)) {
                    if ($productSupplier->pivot->stock_quantity >= $item->quantity) {
                        $inStockItems++;
                    }
                }
            }
        }

        if ($totalItems === 0) {
            return 50;
        }

        return round(($inStockItems / $totalItems) * 100, 2);
    }

    /**
     * Calculate validity score (longer validity = higher score).
     */
    public function calculateValidityScore(): float
    {
        if (!$this->valid_until) {
            return 50; // Neutral if no expiry
        }

        $daysUntilExpiry = now()->diffInDays($this->valid_until, false);

        if ($daysUntilExpiry < 0) {
            return 0; // Expired
        }

        // Score based on validity period
        // 30+ days = 100, 15-29 = 80, 8-14 = 60, 1-7 = 40
        return match(true) {
            $daysUntilExpiry >= 30 => 100,
            $daysUntilExpiry >= 15 => 80,
            $daysUntilExpiry >= 8 => 60,
            $daysUntilExpiry >= 1 => 40,
            default => 0,
        };
    }

    /**
     * Get score breakdown for detailed analysis.
     */
    public function getScoreBreakdown(array $allQuotations = []): array
    {
        return [
            'price' => $this->calculatePriceScore($allQuotations),
            'lead_time' => $this->calculateLeadTimeScore(),
            'supplier' => $this->calculateSupplierScore(),
            'stock' => $this->calculateStockScore(),
            'validity' => $this->calculateValidityScore(),
            'total' => $this->calculateScore($allQuotations),
        ];
    }

    /**
     * Check if this is the best value quotation.
     */
    public function isBestValue(): bool
    {
        $allQuotations = Quotation::where('rfq_id', $this->rfq_id)
            ->where('status', 'pending')
            ->get();

        if ($allQuotations->isEmpty()) {
            return false;
        }

        $scores = $allQuotations->mapWithKeys(function ($quotation) use ($allQuotations) {
            return [$quotation->id => $quotation->calculateScore($allQuotations->toArray())];
        });

        $maxScore = $scores->max();
        return $this->calculateScore($allQuotations->toArray()) === $maxScore;
    }
}
