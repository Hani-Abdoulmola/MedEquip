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
        // Permission check is handled by route middleware
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

        // Enhanced Search: searches across all product fields and relationships
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                // Product fields
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  // Manufacturer
                  ->orWhereHas('manufacturer', function ($m) use ($search) {
                      $m->where('name', 'like', "%{$search}%");
                  })
                  // Category (name and full path)
                  ->orWhereHas('category', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                  })
                  // Suppliers
                  ->orWhereHas('suppliers', function ($s) use ($search) {
                      $s->where('company_name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
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

        // Eager load parent relationship to avoid N+1 queries and ensure full_path works
        $categories = ProductCategory::active()
            ->with('parent') // Load parent for full_path calculation
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
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        // Permission check is handled by route middleware
        $product->load(['category', 'manufacturer', 'suppliers']);

        // Get filter options for dropdowns
        $categories = ProductCategory::active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($cat) {
                return [$cat->id => $cat->full_path];
            });

        $manufacturers = Manufacturer::active()->pluck('name', 'id');

        return view('admin.products.edit', compact('product', 'categories', 'manufacturers'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        // Permission check is handled by route middleware

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'model' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'review_status' => 'required|in:pending,approved,needs_update,rejected',
            'review_notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'specifications' => 'nullable|array',
            'features' => 'nullable|array',
            'technical_data' => 'nullable|array',
            'certifications' => 'nullable|array',
            'installation_requirements' => 'nullable|string',
            'medical_class' => 'nullable|string',
            'ce_marked' => 'boolean',
            'fda_cleared' => 'boolean',
            'iso_certification' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $validated['updated_by'] = Auth::id();
            // Convert string "1"/"0" to boolean for is_active
            $validated['is_active'] = $request->input('is_active') == '1' || $request->input('is_active') === true || $request->input('is_active') === 1;
            $validated['ce_marked'] = $request->has('ce_marked') || $request->input('ce_marked') == '1';
            $validated['fda_cleared'] = $request->has('fda_cleared') || $request->input('fda_cleared') == '1';

            $product->update($validated);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Clear existing images and add new one
                $product->clearMediaCollection('product_images');
                $product->addMediaFromRequest('image')
                    ->toMediaCollection('product_images');
            }

            // Log activity
            activity('products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties(['product_name' => $product->name])
                ->log('تم تحديث المنتج');

            return redirect()
                ->route('admin.products.show', $product)
                ->with('success', 'تم تحديث المنتج بنجاح');

        } catch (\Throwable $e) {
            Log::error('Product update error', [
                'product_id' => $product->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'فشل تحديث المنتج. يرجى المحاولة مرة أخرى.'
            ])->withInput();
        }
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
                ->route('admin.products.index')
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
