<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Product Catalog Service
 * 
 * Manages the canonical product catalog including:
 * - Duplicate detection using canonical hashing
 * - Product request workflow (approve, merge, reject)
 * - SKU generation
 * - Supplier linking
 */
class ProductCatalogService
{
    /**
     * Generate canonical hash for duplicate detection.
     * Normalizes name, brand, and model to create a consistent hash.
     */
    public function generateCanonicalHash(string $name, ?string $brand = null, ?string $model = null): string
    {
        // Normalize: lowercase, trim, remove extra spaces
        $normalized = strtolower(
            trim($name) . '|' . 
            trim($brand ?? '') . '|' . 
            trim($model ?? '')
        );
        
        // Remove Arabic diacritics for better matching
        $normalized = preg_replace('/[\x{064B}-\x{065F}]/u', '', $normalized);
        
        return hash('sha256', $normalized);
    }

    /**
     * Find potential duplicate products.
     * Returns products that match the canonical hash or have similar names.
     */
    public function findDuplicates(string $name, ?string $brand = null, ?string $model = null): Collection
    {
        $hash = $this->generateCanonicalHash($name, $brand, $model);
        
        return Product::where('canonical_hash', $hash)
            ->orWhere(function ($q) use ($name, $brand, $model) {
                // Fuzzy matching on name
                $q->where('name', 'LIKE', '%' . $name . '%');
                
                // If brand provided, also match
                if ($brand) {
                    $q->where(function ($bq) use ($brand) {
                        $bq->where('brand', 'LIKE', '%' . $brand . '%')
                           ->orWhereNull('brand');
                    });
                }
                
                // If model provided, also match
                if ($model) {
                    $q->where(function ($mq) use ($model) {
                        $mq->where('model', 'LIKE', '%' . $model . '%')
                           ->orWhereNull('model');
                    });
                }
            })
            ->with(['category', 'manufacturer'])
            ->get();
    }

    /**
     * Calculate similarity score between a request and existing product.
     */
    public function calculateSimilarity(ProductRequest $request, Product $product): float
    {
        $score = 0;
        $weights = [
            'name' => 40,
            'brand' => 20,
            'model' => 20,
            'category' => 10,
            'manufacturer' => 10,
        ];

        // Name similarity
        similar_text(strtolower($request->name), strtolower($product->name), $namePercent);
        $score += ($namePercent / 100) * $weights['name'];

        // Brand similarity
        if ($request->brand && $product->brand) {
            similar_text(strtolower($request->brand), strtolower($product->brand), $brandPercent);
            $score += ($brandPercent / 100) * $weights['brand'];
        } elseif (!$request->brand && !$product->brand) {
            $score += $weights['brand'];
        }

        // Model similarity
        if ($request->model && $product->model) {
            similar_text(strtolower($request->model), strtolower($product->model), $modelPercent);
            $score += ($modelPercent / 100) * $weights['model'];
        } elseif (!$request->model && !$product->model) {
            $score += $weights['model'];
        }

        // Category match
        if ($request->category_id === $product->category_id) {
            $score += $weights['category'];
        }

        // Manufacturer match
        if ($request->manufacturer_id === $product->manufacturer_id) {
            $score += $weights['manufacturer'];
        }

        return round($score, 2);
    }

    /**
     * Check for duplicates when submitting a product request.
     * Automatically marks request as duplicate if high similarity found.
     */
    public function checkForDuplicatesOnSubmit(ProductRequest $request): ?Product
    {
        $duplicates = $this->findDuplicates($request->name, $request->brand, $request->model);
        
        if ($duplicates->isEmpty()) {
            return null;
        }

        // Find best match
        $bestMatch = null;
        $highestScore = 0;

        foreach ($duplicates as $product) {
            $score = $this->calculateSimilarity($request, $product);
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $product;
            }
        }

        // If similarity > 85%, mark as duplicate
        if ($highestScore >= 85 && $bestMatch) {
            $request->markAsDuplicate($bestMatch, $highestScore);
            return $bestMatch;
        }

        return null;
    }

    /**
     * Generate unique SKU for a product.
     */
    public function generateSku(?int $categoryId = null, ?int $manufacturerId = null): string
    {
        $prefix = 'MED';
        
        // Add category prefix if available
        if ($categoryId) {
            $prefix .= str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        }
        
        // Add sequential number
        $lastSku = Product::whereNotNull('sku')
            ->where('sku', 'LIKE', $prefix . '%')
            ->orderByDesc('sku')
            ->value('sku');
        
        if ($lastSku) {
            $lastNumber = (int) substr($lastSku, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a canonical product (Admin only).
     */
    public function createCanonicalProduct(array $data, User $admin): Product
    {
        $data['source'] = $data['source'] ?? 'admin';
        $data['review_status'] = Product::REVIEW_APPROVED;
        $data['created_by'] = $admin->id;
        $data['canonical_hash'] = $this->generateCanonicalHash(
            $data['name'],
            $data['brand'] ?? null,
            $data['model'] ?? null
        );
        
        // Generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku(
                $data['category_id'] ?? null,
                $data['manufacturer_id'] ?? null
            );
        }
        
        return Product::create($data);
    }

    /**
     * Create product from seeder (pre-approved).
     */
    public function createSeededProduct(array $data): Product
    {
        $data['source'] = 'seeder';
        $data['review_status'] = Product::REVIEW_APPROVED;
        $data['is_active'] = true;
        $data['canonical_hash'] = $this->generateCanonicalHash(
            $data['name'],
            $data['brand'] ?? null,
            $data['model'] ?? null
        );
        
        // Generate SKU
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku(
                $data['category_id'] ?? null,
                $data['manufacturer_id'] ?? null
            );
        }
        
        return Product::create($data);
    }

    /**
     * Link supplier to an existing product with offer data.
     */
    public function linkSupplierToProduct(
        Supplier $supplier,
        Product $product,
        array $offerData
    ): void {
        // Check if already linked
        if ($supplier->products()->where('products.id', $product->id)->exists()) {
            throw new \Exception('المورد مرتبط بهذا المنتج مسبقاً');
        }
        
        $supplier->products()->attach($product->id, [
            'price' => $offerData['price'] ?? 0,
            'stock_quantity' => $offerData['stock_quantity'] ?? 0,
            'lead_time' => $offerData['lead_time'] ?? null,
            'warranty' => $offerData['warranty'] ?? null,
            'status' => $offerData['status'] ?? 'available',
            'notes' => $offerData['notes'] ?? null,
        ]);
    }

    /**
     * Process product request approval.
     */
    public function approveRequest(ProductRequest $request, User $admin, ?string $notes = null): Product
    {
        if (!$request->canBeReviewed()) {
            throw new \Exception('لا يمكن مراجعة هذا الطلب');
        }

        DB::beginTransaction();
        try {
            // Create the canonical product
            $product = $request->approve($admin, $notes);
            
            // Generate SKU
            $product->update([
                'sku' => $this->generateSku($product->category_id, $product->manufacturer_id),
                'canonical_hash' => $this->generateCanonicalHash(
                    $product->name,
                    $product->brand,
                    $product->model
                ),
            ]);
            
            // Auto-link supplier to the new product
            $this->linkSupplierToProduct($request->supplier, $product, [
                'price' => $request->proposed_price,
                'stock_quantity' => $request->proposed_stock,
                'lead_time' => $request->proposed_lead_time,
                'warranty' => $request->proposed_warranty,
                'status' => 'available',
            ]);
            
            // Notify supplier
            NotificationService::send(
                $request->supplier->user,
                '✅ تمت الموافقة على منتجك',
                "تمت الموافقة على طلب إضافة المنتج: {$product->name}. يمكنك الآن إدارة عرضك.",
                route('supplier.products.show', $product->id)
            );
            
            DB::commit();
            return $product;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product request approval failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process product request merge.
     */
    public function mergeRequest(
        ProductRequest $request,
        Product $existingProduct,
        User $admin,
        ?string $notes = null
    ): void {
        if (!$request->canBeReviewed()) {
            throw new \Exception('لا يمكن مراجعة هذا الطلب');
        }

        DB::beginTransaction();
        try {
            $request->merge($admin, $existingProduct, $notes);
            
            // Link supplier to existing product
            $this->linkSupplierToProduct($request->supplier, $existingProduct, [
                'price' => $request->proposed_price,
                'stock_quantity' => $request->proposed_stock,
                'lead_time' => $request->proposed_lead_time,
                'warranty' => $request->proposed_warranty,
                'status' => 'available',
            ]);
            
            // Notify supplier
            NotificationService::send(
                $request->supplier->user,
                '🔗 تم ربط منتجك',
                "تم ربط طلبك بمنتج موجود: {$existingProduct->name}. يمكنك الآن إدارة عرضك.",
                route('supplier.products.show', $existingProduct->id)
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product request merge failed', [
                'request_id' => $request->id,
                'product_id' => $existingProduct->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process product request rejection.
     */
    public function rejectRequest(
        ProductRequest $request,
        User $admin,
        string $reason,
        ?string $notes = null
    ): void {
        if (!$request->canBeReviewed()) {
            throw new \Exception('لا يمكن مراجعة هذا الطلب');
        }

        $request->reject($admin, $reason, $notes);
        
        // Notify supplier
        NotificationService::send(
            $request->supplier->user,
            '❌ تم رفض طلب منتجك',
            "تم رفض طلب إضافة المنتج: {$request->name}. السبب: {$reason}",
            route('supplier.product-requests.index')
        );
    }

    /**
     * Get catalog statistics for admin dashboard.
     */
    public function getCatalogStats(): array
    {
        return [
            'total_products' => Product::count(),
            'approved_products' => Product::approved()->count(),
            'pending_products' => Product::pending()->count(),
            'admin_created' => Product::where('source', 'admin')->count(),
            'supplier_created' => Product::where('source', 'supplier_request')->count(),
            'seeded' => Product::where('source', 'seeder')->count(),
            'pending_requests' => ProductRequest::pending()->count(),
            'duplicate_requests' => ProductRequest::where('status', 'duplicate')->count(),
        ];
    }
}

