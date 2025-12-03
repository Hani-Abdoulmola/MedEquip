<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * 📦 عرض قائمة المنتجات (Admin View Only)
     */
    public function index()
    {
        $query = Product::with(['category', 'creator', 'updater']);

        // 🔍 Filter by supplier
        if (request()->filled('supplier')) {
            $query->whereHas('suppliers', function ($q) {
                $q->where('suppliers.id', request('supplier'));
            });
        }

        // 🔍 Filter by category
        if (request()->filled('category')) {
            $query->where('category_id', request('category'));
        }

        // 🔍 Filter by status
        if (request()->filled('status')) {
            $map = ['active' => true, 'inactive' => false];
            if (isset($map[request('status')])) {
                $query->where('is_active', $map[request('status')]);
            }
        }

        // 🔍 Filter by review_status
        if (request()->filled('review_status')) {
            $query->where('review_status', request('review_status'));
        }

        // 🔍 Search
        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('model', 'like', "%$s%")
                  ->orWhere('brand', 'like', "%$s%");
            });
        }

        $products = $query->latest('id')->paginate(15);

        // Stats
        $stats = [
            'total_products'    => Product::count(),
            'active_products'   => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'total_categories'  => ProductCategory::count(),
        ];

        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->pluck('company_name', 'id');

        $categories = ProductCategory::where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.products.index', compact('products', 'stats', 'suppliers', 'categories'));
    }

    /**
     * 👁️ عرض تفاصيل المنتج
     */
    public function show(Product $product)
    {
        $product->load(['category', 'suppliers', 'creator', 'updater']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * 🔍 صفحة مراجعة المنتج
     */
    public function review(Product $product)
    {
        $product->load(['category', 'suppliers', 'creator']);
        return view('admin.products.review', compact('product'));
    }

    public function approve(Product $product)
    {
        $product->update([
            'review_status' => 'approved',
            'rejection_reason' => null,
            'review_notes' => null,
        ]);

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '✔ تم اعتماد المنتج بنجاح');
    }

    public function reject(Product $product)
    {
        request()->validate(['reason' => 'required|string|max:500']);

        $product->update([
            'review_status' => 'rejected',
            'rejection_reason' => request('reason'),
        ]);

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '❌ تم رفض المنتج');
    }

    public function requestChanges(Product $product)
    {
        request()->validate(['notes' => 'required|string|max:500']);

        $product->update([
            'review_status' => 'needs_update',
            'review_notes' => request('notes'),
        ]);

        return redirect()
            ->route('admin.products.review', $product->id)
            ->with('success', '✏ تم إرسال طلب التعديلات للمورد');
    }

    /**
     * 🗑️ حذف منتج (Soft Delete)
     */
    public function destroy(Product $product)
    {
        try {
            // 🚫 منع حذف منتج قيد المراجعة
            if ($product->review_status === 'pending') {
                return back()->withErrors(['error' => '❌ لا يمكن حذف منتج قيد المراجعة']);
            }

            $productName = $product->name;
            $product->delete();

            activity('products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties(['product_name' => $productName])
                ->log('❌ تم حذف المنتج');

            return redirect()
                ->route('admin.products')
                ->with('success', '❌ تم حذف المنتج بنجاح');

        } catch (\Throwable $e) {
            Log::error('Product delete error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'فشل حذف المنتج']);
        }
    }
}
