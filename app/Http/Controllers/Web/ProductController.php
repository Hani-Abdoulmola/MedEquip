<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📦 عرض قائمة المنتجات (Admin View Only)
     */
    public function index()
    {
        $query = Product::with(['category', 'creator', 'updater']);

        // 🔍 Filter by supplier (from product_supplier pivot)
        if (request()->filled('supplier')) {
            $query->whereHas('suppliers', function ($q) {
                $q->where('suppliers.id', request('supplier'));
            });
        }

        // 🔍 Filter by category
        if (request()->filled('category')) {
            $query->where('category_id', request('category'));
        }

        // 🔍 Filter by status (is_active)
        if (request()->filled('status')) {
            $statusMap = [
                'active' => true,
                'inactive' => false,
            ];
            if (isset($statusMap[request('status')])) {
                $query->where('is_active', $statusMap[request('status')]);
            }
        }

        // 🔍 Search by name, model, or brand
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $products = $query->latest('id')->paginate(15);

        // 📊 Calculate stats
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'total_categories' => ProductCategory::count(),
        ];

        // Get filter options
        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->pluck('company_name', 'id');

        $categories = ProductCategory::where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.products.index', compact('products', 'stats', 'suppliers', 'categories'));
    }

    /**
     * 👁️ عرض تفاصيل المنتج (Admin View)
     */
    public function show(Product $product)
    {
        $product->load(['category', 'suppliers', 'creator', 'updater']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * ✏️ صفحة تعديل المنتج (Admin Edit)
     */
    public function edit(Product $product)
    {
        $product->load(['category', 'suppliers']);

        $categories = ProductCategory::where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * 🔄 تحديث بيانات المنتج (Admin Update)
     */
    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            // Update product basic information
            $product->update([
                'name' => $data['name'],
                'model' => $data['model'] ?? $product->model,
                'brand' => $data['brand'] ?? $product->brand,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'description' => $data['description'] ?? $product->description,
                'is_active' => $data['is_active'] ?? $product->is_active,
                'updated_by' => auth()->id(),
            ]);

            // 🖼️ Handle image upload if provided
            if ($request->hasFile('image')) {
                $product->clearMediaCollection('product_images');
                $product->addMediaFromRequest('image')->toMediaCollection('product_images');
            }

            // 🧾 Log activity
            activity('products')
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'product_name' => $product->name,
                    'updated_by' => auth()->user()->name,
                ])
                ->log('🟡 تم تحديث بيانات المنتج');

            // 🔔 Notify product creator (if supplier)
            if ($product->creator && $product->creator->hasRole('Supplier')) {
                NotificationService::send(
                    $product->creator,
                    '✏️ تم تحديث منتجك',
                    "تم تعديل بيانات المنتج {$product->name} من قبل الإدارة.",
                    route('dashboard')
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.products')
                ->with('success', '✅ تم تحديث بيانات المنتج بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product update error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل تحديث المنتج: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * 🗑️ حذف المنتج (Soft Delete)
     */
    public function destroy(Product $product)
    {
        try {
            $productName = $product->name;

            // Soft delete the product
            $product->delete();

            // 🧾 Log activity
            activity('products')
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['product_name' => $productName])
                ->log('❌ تم حذف المنتج');

            // 🔔 Notify product creator (if supplier)
            if ($product->creator && $product->creator->hasRole('Supplier')) {
                NotificationService::send(
                    $product->creator,
                    '🗑️ تم حذف منتجك',
                    "تم حذف المنتج {$productName} من قبل الإدارة.",
                    route('dashboard')
                );
            }

            return redirect()
                ->route('admin.products')
                ->with('success', '❌ تم حذف المنتج بنجاح');
        } catch (\Throwable $e) {
            Log::error('Product delete error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل حذف المنتج: '.$e->getMessage(),
            ]);
        }
    }
}
