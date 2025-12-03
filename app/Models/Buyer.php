<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    // 🔗 العلاقات
    // ======================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function rfqs()
    {
        return $this->hasMany(Rfq::class, 'buyer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get all invoices for this buyer through orders
     * Note: invoices table doesn't have buyer_id, so we use hasManyThrough
     */
    public function invoices()
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

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'buyer_id');
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

    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    public function activities()
    {
        return $this->morphMany
        (\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }
}
