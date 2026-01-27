<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\BuyerProductService;
use App\Services\BuyerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Buyer Product Controller (Phase 1: thin, delegates to services)
 */
class BuyerProductController extends Controller
{
    public function __construct(
        protected BuyerService $buyerService,
        protected BuyerProductService $productService,
        protected \App\Services\BuyerAlertService $alertService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('browse', Product::class);
        $buyer = Auth::user()->buyerProfile;

        $filters = $request->only([
            'category', 'parent_category', 'manufacturer',
            'min_price', 'max_price', 'stock_status', 'lead_time', 'search',
            'sort', 'direction',
        ]);
        $result = $this->productService->browseProducts(
            $filters,
            (int) $request->get('per_page', 12),
            $buyer
        );

        return view('buyer.products.index', $result);
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);
        $buyer = Auth::user()->buyerProfile;

        if (!$product->is_active || $product->review_status !== 'approved') {
            abort(404, 'المنتج غير متوفر');
        }

        $result = $this->productService->getProductDetails($product->id, $buyer);
        $result['relatedProducts'] = $result['related'];
        unset($result['related']);
        return view('buyer.products.show', $result);
    }

    public function toggleFavorite(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);
        $buyer = Auth::user()->buyerProfile;
        $result = $this->buyerService->toggleFavorite($buyer, $product->id);

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

    public function favorites(Request $request): View
    {
        $this->authorize('browse', Product::class);
        $buyer = Auth::user()->buyerProfile;
        $favorites = $this->buyerService->getFavoriteProducts($buyer, 12);
        return view('buyer.products.favorites', compact('favorites'));
    }

    public function compare(Request $request): View
    {
        $this->authorize('compare', Product::class);
        $productIds = $request->get('products', []);
        if (!is_array($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_slice($productIds, 0, 4);

        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->with([
                'category', 'manufacturer', 'media',
                'suppliers' => fn($q) => $q->where('product_supplier.status', 'available')
                    ->where('suppliers.is_verified', true)
                    ->withPivot(['price', 'stock_quantity', 'lead_time', 'warranty']),
            ])
            ->get();

        return view('buyer.products.compare', compact('products'));
    }

    public function createRfqWithProduct(Product $product): RedirectResponse
    {
        $this->authorize('createRfq', $product);
        session()->flash('rfq_product', [
            'id' => $product->id,
            'name' => $product->name,
            'model' => $product->model,
            'brand' => $product->brand,
        ]);
        return redirect()->route('buyer.rfqs.create')
            ->with('info', "سيتم إضافة المنتج '{$product->name}' تلقائياً لطلب عرض السعر");
    }

    /**
     * Set price alert for a product (Phase 3).
     */
    public function setPriceAlert(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);
        $buyer = Auth::user()->buyerProfile;

        $validated = $request->validate([
            'target_price' => 'required|numeric|min:0',
        ]);

        $this->alertService->setPriceAlert($buyer, $product, (float) $validated['target_price']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تعيين تنبيه السعر بنجاح']);
        }
        return back()->with('success', 'تم تعيين تنبيه السعر بنجاح');
    }

    /**
     * Remove price alert (Phase 3).
     */
    public function removePriceAlert(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);
        $buyer = Auth::user()->buyerProfile;

        $this->alertService->removePriceAlert($buyer, $product->id);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم إزالة تنبيه السعر']);
        }
        return back()->with('success', 'تم إزالة تنبيه السعر');
    }

    /**
     * Set stock alert for a product (Phase 3).
     */
    public function setStockAlert(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);
        $buyer = Auth::user()->buyerProfile;

        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $this->alertService->setStockAlert($buyer, $product, $validated['supplier_id'] ?? null);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تعيين تنبيه المخزون بنجاح']);
        }
        return back()->with('success', 'تم تعيين تنبيه المخزون بنجاح');
    }

    /**
     * Remove stock alert (Phase 3).
     */
    public function removeStockAlert(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('favorite', $product);
        $buyer = Auth::user()->buyerProfile;

        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $this->alertService->removeStockAlert($buyer, $product->id, $validated['supplier_id'] ?? null);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم إزالة تنبيه المخزون']);
        }
        return back()->with('success', 'تم إزالة تنبيه المخزون');
    }
}
