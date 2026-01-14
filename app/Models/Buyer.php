<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Buyer extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'organization_name',
        'organization_type',
        'license_number',
        'country',
        'city',
        'address',
        'contact_email',
        'contact_phone',
        'is_verified',
        'verified_at',
        'is_active',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime:Y-m-d H:i',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ======================
    // 🏢 Organization Type Constants
    // ======================

    public const TYPE_HOSPITAL = 'hospital';
    public const TYPE_CLINIC = 'clinic';
    public const TYPE_PHARMACY = 'pharmacy';
    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_MEDICAL_CENTER = 'medical_center';
    public const TYPE_DISTRIBUTOR = 'distributor';
    public const TYPE_OTHER = 'other';

    public const ORGANIZATION_TYPES = [
        self::TYPE_HOSPITAL => 'مستشفى',
        self::TYPE_CLINIC => 'عيادة',
        self::TYPE_PHARMACY => 'صيدلية',
        self::TYPE_LABORATORY => 'مختبر',
        self::TYPE_MEDICAL_CENTER => 'مركز طبي',
        self::TYPE_DISTRIBUTOR => 'موزع',
        self::TYPE_OTHER => 'أخرى',
    ];

    // ======================
    // 🔗 العلاقات
    // ======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get all invoices for this buyer through orders
     * Note: invoices table doesn't have buyer_id, so we use hasManyThrough
     */
    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Invoice::class,
            Order::class,
            'buyer_id',      // Foreign key on orders table
            'order_id',      // Foreign key on invoices table
            'id',            // Local key on buyers table
            'id'             // Local key on orders table
        );
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'buyer_id');
    }

    /**
     * Get buyer's favorite products.
     */
    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'buyer_favorites')
            ->withTimestamps();
    }

    /**
     * Get buyer favorites (pivot records).
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(BuyerFavorite::class, 'buyer_id');
    }

    // ======================
    // 🔍 Query Scopes
    // ======================

    /**
     * Scope to get only verified buyers.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get only active buyers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get buyers by country.
     */
    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to get buyers by city.
     */
    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    /**
     * Scope to get buyers by organization type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('organization_type', $type);
    }

    /**
     * Scope to search buyers by name, email, or license number.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('organization_name', 'like', "%{$search}%")
              ->orWhere('contact_email', 'like', "%{$search}%")
              ->orWhere('license_number', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope to get buyers pending verification.
     */
    public function scopePendingVerification(Builder $query): Builder
    {
        return $query->where('is_verified', false)
                     ->whereNull('rejection_reason');
    }

    /**
     * Scope to get rejected buyers.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereNotNull('rejection_reason');
    }

    // ======================
    // 📎 إدارة المرفقات (Spatie Media Library)
    // ======================

    public function registerMediaCollections(): void
    {
        // وثائق الترخيص والتحقق
        $this->addMediaCollection('license_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->withResponsiveImages();

        // مرفقات أخرى تخص المشتري (اختياري)
        $this->addMediaCollection('buyer_attachments')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->withResponsiveImages();
    }

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

    /**
     * Check if the buyer is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * Check if the buyer is active.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if the buyer can perform actions (verified and active).
     */
    public function canPerformActions(): bool
    {
        return $this->isVerified() && $this->isActive();
    }

    /**
     * Get the organization type label.
     */
    public function getOrganizationTypeLabelAttribute(): string
    {
        return self::ORGANIZATION_TYPES[$this->organization_type] ?? $this->organization_type;
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        return trim("{$this->address}, {$this->city}, {$this->country}");
    }

    /**
     * Get the display name (organization name or user name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->organization_name ?: ($this->user?->name ?? 'مشتري');
    }

    /**
     * Get the verification status label.
     */
    public function getVerificationStatusAttribute(): string
    {
        if ($this->rejection_reason) {
            return 'مرفوض';
        }
        return $this->is_verified ? 'موثق' : 'قيد المراجعة';
    }

    /**
     * Get the verification status color.
     */
    public function getVerificationStatusColorAttribute(): string
    {
        if ($this->rejection_reason) {
            return 'red';
        }
        return $this->is_verified ? 'green' : 'yellow';
    }

    // ======================
    // 📊 Statistics Methods
    // ======================

    /**
     * Get total RFQs count.
     */
    public function getTotalRfqsCount(): int
    {
        return $this->rfqs()->count();
    }

    /**
     * Get open RFQs count.
     */
    public function getOpenRfqsCount(): int
    {
        return $this->rfqs()->where('status', 'open')->count();
    }

    /**
     * Get pending quotations count (for buyer's RFQs).
     */
    public function getPendingQuotationsCount(): int
    {
        return Quotation::whereHas('rfq', function ($q) {
            $q->where('buyer_id', $this->id);
        })->where('status', 'pending')->count();
    }

    /**
     * Get total orders count.
     */
    public function getTotalOrdersCount(): int
    {
        return $this->orders()->count();
    }

    /**
     * Get total spending.
     */
    public function getTotalSpending(): float
    {
        return $this->orders()->sum('total_amount') ?? 0;
    }

    /**
     * Get favorites count.
     */
    public function getFavoritesCount(): int
    {
        return $this->favorites()->count();
    }

    /**
     * Check if a product is in favorites.
     */
    public function hasFavorite(int $productId): bool
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }

    // ======================
    // 🔗 Activity Log
    // ======================

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
}
