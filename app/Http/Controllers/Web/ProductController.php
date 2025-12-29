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
     * Show the review page for a product.
     *
     * @param Product $product
     * @return View
     */
    public function review(Product $product): View
    {
        $product->load(['category', 'manufacturer', 'suppliers', 'creator']);
        
        return view('admin.products.review', compact('product'));
    }

    /**
     * Approve a product after review.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function approve(Product $product): RedirectResponse
    {
        $product->update([
            'review_status' => Product::REVIEW_APPROVED,
            'rejection_reason' => null,
            'review_notes' => null,
        ]);

        // Log activity
        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['product_name' => $product->name])
            ->log('✔ تم اعتماد المنتج');

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '✔ تم اعتماد المنتج بنجاح');
    }

    /**
     * Reject a product with a reason.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function reject(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ], [
            'reason.required' => 'يجب إدخال سبب الرفض',
            'reason.max' => 'سبب الرفض يجب ألا يتجاوز 500 حرف',
        ]);

        $product->update([
            'review_status' => Product::REVIEW_REJECTED,
            'rejection_reason' => $request->reason,
        ]);

        // Log activity
        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties([
                'product_name' => $product->name,
                'reason' => $request->reason
            ])
            ->log('❌ تم رفض المنتج');

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '❌ تم رفض المنتج');
    }

    /**
     * Request changes for a product.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function requestChanges(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|max:500'
        ], [
            'notes.required' => 'يجب إدخال ملاحظات التعديل',
            'notes.max' => 'ملاحظات التعديل يجب ألا تتجاوز 500 حرف',
        ]);

        $product->update([
            'review_status' => Product::REVIEW_NEEDS_UPDATE,
            'review_notes' => $request->notes,
        ]);

        // Log activity
        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties([
                'product_name' => $product->name,
                'notes' => $request->notes
            ])
            ->log('✏ تم طلب تعديلات على المنتج');

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '✏ تم إرسال طلب التعديلات');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
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
