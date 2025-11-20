<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use Auditable, HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_type_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * تشفير كلمة المرور تلقائيًا
     */
    public function setPasswordAttribute($value)
    {
        if (! empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Accessor for user_type (returns user_type_id for backward compatibility)
     */
    public function getUserTypeAttribute()
    {
        return $this->user_type_id;
    }

    // ---------------- العلاقات ----------------

    public function type()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function supplierProfile()
    {
        return $this->hasOne(Supplier::class, 'user_id');
    }

    public function buyerProfile()
    {
        return $this->hasOne(Buyer::class, 'user_id');
    }

    public function paymentsProcessed()
    {
        return $this->hasMany(Payment::class, 'processed_by');
    }

    // ---------------- Media Collections ----------------

    /**
     * Register media collections for user files
     */
    public function registerMediaCollections(): void
    {
        // Profile photos
        $this->addMediaCollection('profile_photos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();

        // User documents (ID, certificates, etc.)
        $this->addMediaCollection('user_documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->withResponsiveImages();
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('avatar')
            ->width(300)
            ->height(300);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }
}
