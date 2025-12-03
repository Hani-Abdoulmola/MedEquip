<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\SupplierProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierProductController extends Controller
{
    /**
     *  قائمة منتجات المورد
     */
    public function index()
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        $query = $supplier->products()->with('category');

        if (request()->filled('category')) {
            $query->where('category_id', request('category'));
        }
        if (request()->filled('status')) {
            $query->wherePivot('status', request('status'));
        }
        if (request()->filled('review_status')) {
            $query->where('products.review_status', '=', request('review_status'));
        }
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
            );

        }

        $products = $query->latest('product_supplier.created_at')->paginate(15);

        $stats = [
            'total' => $supplier->products()->count(),
            'pending' => $supplier->products()->where('review_status', 'pending')->count(),
            'approved' => $supplier->products()->where('review_status', 'approved')->count(),
            'needs_update' => $supplier->products()->where('review_status', 'needs_update')->count(),
            'rejected' => $supplier->products()->where('review_status', 'rejected')->count(),
        ];

        $categories = ProductCategory::pluck('name', 'id');

        return view('supplier.products.index', compact('products', 'stats', 'categories'));
    }

    /**
     * ➕ صفحة إضافة منتج
     */
    public function create()
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        $existingProducts = Product::where('is_active', true)
            ->whereDoesntHave('suppliers', fn($q) => $q->where('suppliers.id', $supplier->id))
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::pluck('name', 'id');

        return view('supplier.products.create', compact('existingProducts', 'categories'));
    }

    /**
     * 💾 تخزين منتج جديد أو ربط منتج موجود
     */
    public function store(SupplierProductRequest $request)
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        DB::beginTransaction();
        try {
            // —————————— إنشاء منتج جديد
            if ($request->action === 'new') {
                $product = Product::create([
                    'created_by' => Auth::id(),
                    'name' => $request->name,
                    'model' => $request->model,
                    'brand' => $request->brand,
                    'category_id' => $request->category_id,
                    'description' => $request->description,

                    'specifications' => $request->specifications,
                    'features' => $request->features,
                    'technical_data' => $request->technical_data,
                    'certifications' => $request->certifications,
                    'installation_requirements' => $request->installation_requirements,

                    'review_status' => 'pending',
                    'is_active' => true,
                ]);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $img) {
                        $product->addMedia($img)->toMediaCollection('product_images');
                    }
                }
            }

            // —————————— ربط منتج موجود
            else {
                $product = Product::findOrFail($request->product_id);
            }

            // —————————— attach product to supplier
            $supplier->products()->attach($product->id, [
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'lead_time' => $request->lead_time,
                'warranty' => $request->warranty,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('supplier.products.index')->with('success', '✔ تم إضافة المنتج بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier product creation error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة المنتج']);
        }
    }

    /**
     * ✏ صفحة تعديل منتج
     */
    public function edit(Product $product)
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        if (!$supplier->products()->where('products.id', $product->id)->exists()) abort(403);

        $product->load(['category', 'suppliers']);
        $pivot = $supplier->products()->where('products.id', $product->id)->first()->pivot;
        $categories = ProductCategory::pluck('name', 'id');

        return view('supplier.products.edit', compact('product', 'pivot', 'categories'));
    }

    /**
     * 🔄 تحديث المنتج (أساسي + Pivot)
     */
    public function update(SupplierProductRequest $request, Product $product)
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        if (!$supplier->products()->where('products.id', $product->id)->exists()) abort(403);

        DB::beginTransaction();
        try {
            // تحديث المنتج الأساسي
            $product->update([
                'updated_by' => Auth::id(),
                'name' => $request->name,
                'model' => $request->model,
                'brand' => $request->brand,
                'category_id' => $request->category_id,
                'description' => $request->description,

                'specifications' => $request->specifications,
                'features' => $request->features,
                'technical_data' => $request->technical_data,
                'certifications' => $request->certifications,
                'installation_requirements' => $request->installation_requirements,

                'review_status' => 'pending',
                'review_notes' => null,
                'rejection_reason' => null,
            ]);

            // تحديث الصور
            if ($request->hasFile('images')) {
                $product->clearMediaCollection('product_images');
                foreach ($request->file('images') as $img) {
                    $product->addMedia($img)->toMediaCollection('product_images');
                }
            }

            // تحديث الـPivot
            $supplier->products()->updateExistingPivot($product->id, [
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'lead_time' => $request->lead_time,
                'warranty' => $request->warranty,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()
                ->route('supplier.products.index')
                ->with('success', '✔ تم تحديث المنتج — بانتظار موافقة الإدارة');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier product update error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث المنتج']);
        }
    }

    /**
     * ❌ إزالة المنتج من المورد (detach)
     */
    public function destroy(Product $product)
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$supplier) abort(403);

        if (!$supplier->products()->where('products.id', $product->id)->exists()) abort(403);

        $supplier->products()->detach($product->id);

        return redirect()->route('supplier.products.index')
            ->with('success', '❌ تم حذف المنتج من قائمتك');
    }
}
