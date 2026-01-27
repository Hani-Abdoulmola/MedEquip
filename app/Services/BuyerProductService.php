<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Buyer Product Service
 *
 * Handles product catalog browsing, filtering, and detail view for buyers.
 * Phase 1: Separation of concerns from BuyerProductController.
 */
class BuyerProductService
{
    private const CACHE_FILTERS_TTL = 3600; // 1 hour

    /**
     * Browse products with filters and pagination.
     *
     * @return array{products: LengthAwarePaginator, categories: Collection, manufacturers: Collection, favoriteIds: array<int>}
     */
    public function browseProducts(array $filters, int $perPage = 12, ?Buyer $buyer = null): array
    {
        $query = Product::query()
            ->where('is_active', true)
            ->where('review_status', 'approved');

        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters);

        $query->with(['category', 'manufacturer', 'media']);
        $query->withCount(['suppliers' => function ($q) {
            $q->where('product_supplier.status', 'available')
                ->where('suppliers.is_verified', true)
                ->where('suppliers.is_active', true);
        }]);

        $products = $query->paginate($perPage)->withQueryString();

        $categories = $this->getCategories();
        $manufacturers = $this->getManufacturers();
        $favoriteIds = $buyer ? $buyer->favoriteProducts()->pluck('products.id')->toArray() : [];

        return compact('products', 'categories', 'manufacturers', 'favoriteIds');
    }

    /**
     * Get product details for show page.
     *
     * @return array{product: Product, related: Collection, isFavorite: bool}
     */
    public function getProductDetails(int $productId, ?Buyer $buyer = null): array
    {
        $product = Product::query()
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->with([
                'category',
                'manufacturer',
                'media',
                'suppliers' => function ($q) {
                    $q->where('product_supplier.status', 'available')
                        ->where('suppliers.is_verified', true)
                        ->where('suppliers.is_active', true)
                        ->withPivot(['price', 'stock_quantity', 'lead_time', 'warranty', 'notes'])
                        ->orderBy('product_supplier.price');
                },
            ])
            ->findOrFail($productId);

        $this->enrichSuppliersWithValueScore($product);

        $related = $this->getRelatedProducts($product->category_id, $product->id, 4);
        $isFavorite = $buyer ? app(BuyerService::class)->isFavorite($buyer, $product->id) : false;

        return compact('product', 'related', 'isFavorite');
    }

    /**
     * Get related products by category.
     */
    public function getRelatedProducts(?int $categoryId, int $excludeId, int $limit = 4): Collection
    {
        if (!$categoryId) {
            return new Collection;
        }

        return Product::query()
            ->where('category_id', $categoryId)
            ->where('id', '!=', $excludeId)
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->with(['media', 'category'])
            ->limit($limit)
            ->get();
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        } elseif (!empty($filters['parent_category'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('category_id', $filters['parent_category'])
                    ->orWhereHas('category', fn($c) => $c->where('parent_id', $filters['parent_category']));
            });
        }

        if (!empty($filters['manufacturer'])) {
            $query->where('manufacturer_id', $filters['manufacturer']);
        }

        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereHas('suppliers', function ($q) use ($filters) {
                $q->where('product_supplier.status', 'available')
                    ->where('suppliers.is_verified', true)
                    ->where('suppliers.is_active', true);
                if (isset($filters['min_price'])) {
                    $q->where('product_supplier.price', '>=', $filters['min_price']);
                }
                if (isset($filters['max_price'])) {
                    $q->where('product_supplier.price', '<=', $filters['max_price']);
                }
            });
        }

        if (!empty($filters['stock_status'])) {
            $status = $filters['stock_status'];
            $query->whereHas('suppliers', function ($q) use ($status) {
                $q->where('product_supplier.status', 'available')
                    ->where('suppliers.is_verified', true)
                    ->where('suppliers.is_active', true);
                match ($status) {
                    'in_stock' => $q->where('product_supplier.stock_quantity', '>', 0),
                    'low_stock' => $q->whereBetween('product_supplier.stock_quantity', [1, 10]),
                    'out_of_stock' => $q->where('product_supplier.stock_quantity', '<=', 0),
                    default => null,
                };
            });
        }

        if (!empty($filters['lead_time'])) {
            $lead = $filters['lead_time'];
            $query->whereHas('suppliers', function ($q) use ($lead) {
                $q->where('product_supplier.status', 'available')
                    ->where('suppliers.is_verified', true)
                    ->where('suppliers.is_active', true);
                $expr = "COALESCE(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(product_supplier.lead_time), ' ', 1), '-', 1) AS UNSIGNED), 999)";
                match ($lead) {
                    'fast' => $q->whereRaw("{$expr} <= 7"),
                    'medium' => $q->whereRaw("{$expr} BETWEEN 8 AND 14"),
                    'standard' => $q->whereRaw("{$expr} BETWEEN 15 AND 30"),
                    'extended' => $q->whereRaw("{$expr} > 30"),
                    default => null,
                };
            });
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%");
            });
        }
    }

    private function applySort($query, array $filters): void
    {
        $sort = $filters['sort'] ?? 'created_at';
        $dir = $filters['direction'] ?? 'desc';

        if ($sort === 'suppliers') {
            $query->orderBy('suppliers_count', 'desc');
        } elseif ($sort === 'price_asc') {
            if ($this->useDenormalizedCounts()) {
                $query->orderBy('min_price', 'asc');
            } else {
                $query->addSelect([
                    'min_price' => DB::table('product_supplier')
                        ->selectRaw('MIN(price)')
                        ->whereColumn('product_supplier.product_id', 'products.id')
                        ->where('product_supplier.status', 'available')
                        ->limit(1),
                ])->orderBy('min_price', 'asc');
            }
        } elseif ($sort === 'price_desc') {
            if ($this->useDenormalizedCounts()) {
                $query->orderBy('min_price', 'desc');
            } else {
                $query->addSelect([
                    'min_price' => DB::table('product_supplier')
                        ->selectRaw('MIN(price)')
                        ->whereColumn('product_supplier.product_id', 'products.id')
                        ->where('product_supplier.status', 'available')
                        ->limit(1),
                ])->orderBy('min_price', 'desc');
            }
        } elseif (in_array($sort, ['name', 'created_at', 'updated_at'])) {
            $query->orderBy($sort, $sort === 'name' ? 'asc' : $dir);
        } else {
            $query->latest();
        }
    }

    private function useDenormalizedCounts(): bool
    {
        return \Schema::hasColumn('products', 'min_price');
    }

    private function enrichSuppliersWithValueScore(Product $product): void
    {
        $suppliers = $product->suppliers;
        foreach ($suppliers as $s) {
            $s->value_score = $this->valueScore($s->pivot->price ?? 0, $s->pivot->lead_time ?? null, $s->pivot->warranty ?? null);
            $s->is_best_value = false;
        }
        $best = $suppliers->sortByDesc('value_score')->first();
        if ($best) {
            $best->is_best_value = true;
        }
    }

    private function valueScore(float $price, ?string $leadTime, ?string $warranty): float
    {
        $score = 1000 - min(1000, $price / 10);
        if ($leadTime) {
            $days = (int) preg_replace('/\D/', '', $leadTime);
            if ($days > 0 && $days <= 7) {
                $score += 50;
            } elseif ($days <= 14) {
                $score += 25;
            }
        }
        if ($warranty && stripos($warranty, '2') !== false) {
            $score += 20;
        }
        return $score;
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public function getCategories(): Collection
    {
        return Cache::remember('buyer_product_categories', self::CACHE_FILTERS_TTL, function () {
            return ProductCategory::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Manufacturer>
     */
    public function getManufacturers(): Collection
    {
        return Cache::remember('buyer_product_manufacturers', self::CACHE_FILTERS_TTL, function () {
            return Manufacturer::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    public function forgetFilterCache(): void
    {
        Cache::forget('buyer_product_categories');
        Cache::forget('buyer_product_manufacturers');
    }
}
