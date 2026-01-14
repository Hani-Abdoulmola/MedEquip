<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\Manufacturer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Product Controller
 * 
 * Handles admin operations for product management including viewing,
 * reviewing, approving, rejecting, and deleting products.
 * 
 * @package App\Http\Controllers\Web
 */
class ProductController extends Controller
{
    /**
     * Display a listing of products with filters.
     *
     * @return View
     */
    public function index(): View
    {
        // Check permission
        if (!auth()->user()->can('products.view')) {
            abort(403, 'ليس لديك صلاحية عرض المنتجات');
        }
        
        $query = Product::with(['category', 'manufacturer', 'creator', 'updater']);

        // Filter by supplier
        if (request()->filled('supplier')) {
            $query->whereHas('suppliers', function ($q) {
                $q->where('suppliers.id', request('supplier'));
            });
        }

        // Filter by category
        if (request()->filled('category')) {
            $query->where('category_id', request('category'));
        }

        // Filter by manufacturer
        if (request()->filled('manufacturer')) {
            $query->where('manufacturer_id', request('manufacturer'));
        }

        // Filter by active status
        if (request()->filled('status')) {
            $statusMap = ['active' => true, 'inactive' => false];
            if (isset($statusMap[request('status')])) {
                $query->where('is_active', $statusMap[request('status')]);
            }
        }

        // Filter by review status
        if (request()->filled('review_status')) {
            $query->where('review_status', request('review_status'));
        }

        // Search: name, model, brand, or manufacturer
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('manufacturer', function ($m) use ($search) {
                      $m->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_products'    => Product::count(),
            'active_products'   => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'total_categories'  => ProductCategory::count(),
        ];

        // Get filter options
        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->pluck('company_name', 'id');

        $categories = ProductCategory::active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($cat) {
                return [$cat->id => $cat->full_path];
            });

        $manufacturers = Manufacturer::active()->pluck('name', 'id');

        return view('admin.products.index', compact(
            'products',
            'stats',
            'suppliers',
            'categories',
            'manufacturers'
        ));
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'manufacturer', 'suppliers', 'creator', 'updater']);
        
        return view('admin.products.show', compact('product'));
    }

    /**
     * NOTE: Product review methods (review, approve, reject, requestChanges) 
     * are handled by ProductReviewController to maintain separation of concerns.
     * Routes point to ProductReviewController, not this controller.
     * 
     * These methods were removed to eliminate code duplication and security risks.
     */

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        // CRITICAL FIX: Authorization check - only admins with delete permission can delete products
        Gate::authorize('delete', $product);
        
        try {
            // CRITICAL FIX: Check for active supplier offers before deletion
            $activeOffers = $product->suppliers()
                ->wherePivot('status', 'available')
                ->count();
                
            if ($activeOffers > 0) {
                return back()->withErrors([
                    'error' => '❌ لا يمكن حذف المنتج لأنه مرتبط بعروض نشطة من ' . $activeOffers . ' مورد. قم بإيقاف العروض أولاً.'
                ]);
            }
            
            // Prevent deletion of products under review
            if ($product->review_status === Product::REVIEW_PENDING) {
                return back()->withErrors([
                    'error' => '❌ لا يمكن حذف منتج قيد المراجعة'
                ]);
            }

            $productName = $product->name;
            
            // Delete the product (soft delete)
            $product->delete();

            // Log activity
            activity('products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties(['product_name' => $productName])
                ->log('❌ تم حذف المنتج');

            return redirect()
                ->route('admin.products')
                ->with('success', '❌ تم حذف المنتج بنجاح');

        } catch (\Throwable $e) {
            Log::error('Product deletion error', [
                'product_id' => $product->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'فشل حذف المنتج. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }
}
