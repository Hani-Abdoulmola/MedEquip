<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Manufacturer Model
 * 
 * Represents medical equipment manufacturers with multilingual support.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $name_ar
 * @property string $slug
 * @property int|null $category_id
 * @property string|null $country
 * @property string|null $website
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read ProductCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $products
 */
class Manufacturer extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'category_id',
        'country',
        'website',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and register model events.
     */
    protected static function booted(): void
    {
        static::creating(function (Manufacturer $manufacturer) {
            if (empty($manufacturer->slug)) {
                $manufacturer->slug = static::generateUniqueSlug($manufacturer->name);
            }
        });

        static::updating(function (Manufacturer $manufacturer) {
            // Regenerate slug if name changes and slug is empty
            if ($manufacturer->isDirty('name') && empty($manufacturer->slug)) {
                $manufacturer->slug = static::generateUniqueSlug($manufacturer->name);
            }
        });
    }

    /**
     * Generate a unique slug for the manufacturer.
     *
     * @param string $name The manufacturer name
     * @return string The unique slug
     */
    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Keep incrementing counter until we find a unique slug
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    // ================================
    // Relationships
    // ================================

    /**
     * Get the category that the manufacturer belongs to.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get all products for this manufacturer.
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacturer_id');
    }

    // ================================
    // Query Scopes
    // ================================

    /**
     * Scope to filter only active manufacturers.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by country.
     *
     * @param Builder $query
     * @param string $country
     * @return Builder
     */
    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to filter by category.
     *
     * @param Builder $query
     * @param int $categoryId
     * @return Builder
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get the display name (Arabic if available, otherwise English).
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name_ar ?? $this->name;
    }
}
