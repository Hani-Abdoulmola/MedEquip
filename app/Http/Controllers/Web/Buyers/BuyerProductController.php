<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Services\BuyerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Buyer Product Controller
 * 
 * Handles product catalog browsing for buyers.
 * Buyers can browse approved products, view details, add to favorites,
 * and compare products.
 * 
 * Note: Buyer verification is handled by the 'buyer.verified' middleware.
 */
class BuyerProductController extends Controller
{
    protected BuyerService $buyerService;

    public function __construct(BuyerService $buyerService)
    {
        $this->buyerService = $buyerService;
    }

    /**
     * Display the product catalog.
     */
    public function index(Request $request): View
    {
        $this->authorize('browse', Product::class);

        $buyer = Auth::user()->buyerProfile;

        $query = Product::where('is_active', true)
            ->where('review_status', 'approved')
            ->with(['category', 'manufacturer', 'media', 'suppliers' => function ($q) {
                $q->where('product_supplier.status', 'available')
                  ->where('suppliers.is_verified', true)
                  ->where('suppliers.is_active', true);
            }]);

        // Filter by category
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('category', function ($cat) use ($categoryId) {
                      $cat->where('parent_id', $categoryId);
                  });
            });
        }

        // Filter by manufacturer
        if ($request->filled('manufacturer')) {
            $query->where('manufacturer_id', $request->manufacturer);
        }

        // Filter by price range (from pivot table)
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('suppliers', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('product_supplier.price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('product_supplier.price', '<=', $request->max_price);
                }
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if ($sortField === 'suppliers') {
            // Sort by number of suppliers
            $query->withCount(['suppliers' => fn($q) => 
                $q->where('product_supplier.status', 'available')
                  ->where('suppliers.is_verified', true)
            ])->orderBy('suppliers_count', 'desc');
        } elseif (in_array($sortField, ['name', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortField === 'name' ? 'asc' : $sortDirection);
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // Get buyer's favorite product IDs for quick lookup
        $favoriteIds = $buyer->favoriteProducts()->pluck('products.id')->toArray();

        // Get categories and manufacturers for filters
        $categories = ProductCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        $manufacturers = Manufacturer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('buyer.products.index', compact(
            'products',
            'categories',
            'manufacturers',
            'favoriteIds'
        ));
    }

    /**
     * Display product details.
     */
    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $buyer = Auth::user()->buyerProfile;

        // Only show approved and active products to buyers
        if (!$product->is_active || $product->review_status !== 'approved') {
            abort(404, 'المنتج غير متوفر');
        }

        $product->load([
            'category',
            'manufacturer',
            'media',
            'suppliers' => function ($q) {
                $q->where('product_supplier.status', 'available')
                  ->where('suppliers.is_verified', true)
                  ->where('suppliers.is_active', true)
                  ->withPivot(['price', 'stock_quantity', 'lead_time', 'warranty', 'notes']);
            }
        ]);

        // Check if product is in favorites
        $isFavorite = $this->buyerService->isFavorite($buyer, $product->id);

        // Get related products from same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->with(['media', 'category'])
            ->limit(4)
            ->get();

        return view('buyer.products.show', compact('product', 'isFavorite', 'relatedProducts'));
    }

    /**
     * Toggle product favorite status.
     */
    public function toggleFavorite(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);

        $buyer = Auth::user()->buyerProfile;

        $result = $this->buyerService->toggleFavorite($buyer, $product->id);

        // Log activity
        activity('buyer_favorites')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'action' => $result['added'] ? 'added' : 'removed',
            ])
            ->log($result['added'] ? 'أضاف المشتري المنتج للمفضلة' : 'أزال المشتري المنتج من المفضلة');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'added' => $result['added'],
                'count' => $result['count'],
                'message' => $result['added'] ? 'تمت إضافة المنتج للمفضلة' : 'تمت إزالة المنتج من المفضلة',
            ]);
        }

        return back()->with('success', $result['added'] ? 'تمت إضافة المنتج للمفضلة' : 'تمت إزالة المنتج من المفضلة');
    }

    /**
     * Display buyer's favorite products.
     */
    public function favorites(Request $request): View
    {
        $this->authorize('browse', Product::class);

        $buyer = Auth::user()->buyerProfile;
        $favorites = $this->buyerService->getFavoriteProducts($buyer, 12);

        return view('buyer.products.favorites', compact('favorites'));
    }

    /**
     * Compare multiple products side by side.
     */
    public function compare(Request $request): View
    {
        $this->authorize('compare', Product::class);

        $buyer = Auth::user()->buyerProfile;
        $productIds = $request->get('products', []);
        
        if (!is_array($productIds)) {
            $productIds = explode(',', $productIds);
        }

        // Limit to 4 products for comparison
        $productIds = array_slice($productIds, 0, 4);

        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->with([
                'category',
                'manufacturer',
                'media',
                'suppliers' => function ($q) {
                    $q->where('product_supplier.status', 'available')
                      ->where('suppliers.is_verified', true)
                      ->withPivot(['price', 'stock_quantity', 'lead_time', 'warranty']);
                }
            ])
            ->get();

        return view('buyer.products.compare', compact('products'));
    }

    /**
     * Create RFQ with selected product (redirect to RFQ creation with pre-filled data).
     */
    public function createRfqWithProduct(Product $product): RedirectResponse
    {
        $this->authorize('createRfq', $product);

        $buyer = Auth::user()->buyerProfile;

        // Store product info in session for RFQ creation form
        session()->flash('rfq_product', [
            'id' => $product->id,
            'name' => $product->name,
            'model' => $product->model,
            'brand' => $product->brand,
        ]);

        return redirect()->route('buyer.rfqs.create')
            ->with('info', "سيتم إضافة المنتج '{$product->name}' تلقائياً لطلب عرض السعر");
    }
}

