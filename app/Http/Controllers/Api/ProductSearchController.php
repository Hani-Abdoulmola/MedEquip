<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Product Search API Controller
 * 
 * Provides AJAX endpoints for product searching, filtering, and autocomplete.
 * Used by both buyers (catalog browsing) and suppliers (product linking).
 */
class ProductSearchController extends Controller
{
    /**
     * Search products with filters (for buyers).
     * Returns paginated results with full product data.
     */
    public function search(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)
            ->where('review_status', 'approved')
            ->with(['category', 'manufacturer', 'media']);

        // Text search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('category', fn($cat) => $cat->where('parent_id', $categoryId));
            });
        }

        // Manufacturer filter
        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }

        // Has suppliers filter
        if ($request->boolean('has_suppliers')) {
            $query->whereHas('suppliers', fn($q) => 
                $q->where('product_supplier.status', 'available')
                  ->where('suppliers.is_verified', true)
            );
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        
        if (in_array($sortField, ['name', 'created_at', 'sku'])) {
            $query->orderBy($sortField, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Autocomplete search for product names.
     * Returns minimal data for quick dropdown results.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where('review_status', 'approved')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'model', 'brand', 'sku', 'category_id')
            ->with('category:id,name')
            ->limit(10)
            ->get();

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'model' => $p->model,
            'brand' => $p->brand,
            'sku' => $p->sku,
            'category' => $p->category?->name,
            'label' => $p->name . ($p->model ? " ({$p->model})" : ''),
        ]));
    }

    /**
     * Get available products for supplier linking.
     * Excludes products already linked to the supplier.
     */
    public function forSupplier(Request $request): JsonResponse
    {
        $supplierId = $request->user()?->supplierProfile?->id;
        
        if (!$supplierId) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $query = Product::where('is_active', true)
            ->where('review_status', 'approved')
            ->with(['category:id,name', 'manufacturer:id,name'])
            ->withCount(['suppliers as linked' => fn($q) => $q->where('suppliers.id', $supplierId)]);

        // Text search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Only show products not yet linked
        if ($request->boolean('not_linked')) {
            $query->whereDoesntHave('suppliers', fn($q) => $q->where('suppliers.id', $supplierId));
        }

        $products = $query->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'model' => $p->model,
            'brand' => $p->brand,
            'sku' => $p->sku,
            'category' => $p->category?->name,
            'manufacturer' => $p->manufacturer?->name,
            'is_linked' => $p->linked > 0,
            'image' => $p->getFirstMediaUrl('product_images', 'thumb'),
        ]));
    }

    /**
     * Get filter options (categories, manufacturers, etc.).
     */
    public function filters(): JsonResponse
    {
        $categories = \App\Models\ProductCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'children' => $c->children->map(fn($ch) => [
                    'id' => $ch->id,
                    'name' => $ch->name,
                ]),
            ]);

        $manufacturers = \App\Models\Manufacturer::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'categories' => $categories,
            'manufacturers' => $manufacturers,
        ]);
    }
}

