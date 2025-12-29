<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\SupplierProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Supplier Product Controller
 *
 * Handles product management operations for suppliers including CRUD operations
 * on products and their offers (pivot table data).
 *
 * @package App\Http\Controllers\Web\Suppliers
 */
class SupplierProductController extends Controller
{
    /**
     * Display a listing of the supplier's products.
     *
     * @return View
     */
    public function index(): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = $supplier->products()->with(['category', 'manufacturer']);

        // Filter by category
        if (request()->filled('category')) {
            $query->where('category_id', request('category'));
        }

        // Filter by offer status (pivot table status)
        if (request()->filled('status')) {
            $query->wherePivot('status', request('status'));
        }

        // Filter by review status
        if (request()->filled('review_status')) {
            $query->where('products.review_status', request('review_status'));
        }

        // Search by name, model, or brand
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.model', 'like', "%{$search}%")
                    ->orWhere('products.brand', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if (request()->filled('date_from')) {
            $query->whereDate('product_supplier.created_at', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $query->whereDate('product_supplier.created_at', '<=', request('date_to'));
        }

        $products = $query->latest('product_supplier.created_at')
            ->paginate(15)
            ->withQueryString();

        // Optimized stats calculation using single query
        $stats = $supplier->products()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN review_status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN review_status = ? THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN review_status = ? THEN 1 ELSE 0 END) as needs_update,
                SUM(CASE WHEN review_status = ? THEN 1 ELSE 0 END) as rejected
            ', [
                Product::REVIEW_PENDING,
                Product::REVIEW_APPROVED,
                Product::REVIEW_NEEDS_UPDATE,
                Product::REVIEW_REJECTED,
            ])
            ->first();

        $stats = [
            'total'        => $stats->total ?? 0,
            'pending'      => $stats->pending ?? 0,
            'approved'     => $stats->approved ?? 0,
            'needs_update' => $stats->needs_update ?? 0,
            'rejected'     => $stats->rejected ?? 0,
        ];

        // Get active categories with hierarchy for filter dropdown
        $categories = ProductCategory::active()
            ->with('parent')
            ->ordered()
            ->get()
            ->mapWithKeys(function ($category) {
                $displayName = $category->parent
                    ? $category->parent->name . ' > ' . $category->name
                    : $category->name;
                return [$category->id => $displayName];
            });

        // Log activity
        activity('supplier_products')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => request()->only(['category', 'status', 'review_status', 'search', 'date_from', 'date_to']),
            ])
            ->log('عرض المورد قائمة المنتجات');

        return view('supplier.products.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return View
     */
    public function create(): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Get existing products not yet linked to this supplier
        $existingProducts = Product::where('is_active', true)
            ->whereDoesntHave('suppliers', fn ($q) => $q->where('suppliers.id', $supplier->id))
            ->with(['category', 'manufacturer'])
            ->orderBy('name')
            ->get();

        // Get active categories with hierarchy (parent > child format)
        $categories = ProductCategory::active()
            ->with('parent')
            ->ordered()
            ->get()
            ->mapWithKeys(function ($category) {
                $displayName = $category->parent
                    ? $category->parent->name . ' > ' . $category->name
                    : $category->name;
                return [$category->id => $displayName];
            });

        $manufacturers = Manufacturer::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('supplier.products.create', compact('existingProducts', 'categories', 'manufacturers'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param SupplierProductRequest $request
     * @return RedirectResponse
     */
    public function store(SupplierProductRequest $request): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        DB::beginTransaction();

        try {
            // Create new product or link existing one
            if ($request->action === 'new') {
                // Create new product
                $product = Product::create([
                    'created_by'   => Auth::id(),
                    'name'         => $request->name,
                    'model'        => $request->model,
                    'brand'        => $request->brand,
                    'manufacturer_id' => $request->manufacturer_id,
                    'category_id'  => $request->category_id,
                    'description'  => $request->description,
                    'specifications' => $request->specifications
                    ? array_filter(array_map('trim', explode("\n", $request->specifications)))
                    : null,

                    'features' => $request->features
                    ? array_filter(array_map('trim', explode("\n", $request->features)))
                    : null,

                    'technical_data' => $request->technical_data
                    ? array_filter(array_map('trim', explode("\n", $request->technical_data)))
                    : null,

                    'certifications' => $request->certifications
                    ? array_filter(array_map('trim', explode("\n", $request->certifications)))
                    : null,
                    'installation_requirements' => $request->installation_requirements,
                    'review_status' => Product::REVIEW_PENDING,
                    'is_active'     => true,
                ]);

                // Upload product images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $product->addMedia($image)->toMediaCollection('product_images');
                    }
                }
            } else {
                // Link existing product
                $product = Product::findOrFail($request->product_id);
            }

            // Attach product to supplier with pivot data
            $supplier->products()->attach($product->id, [
                'price'          => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'lead_time'      => $request->lead_time,
                'warranty'       => $request->warranty,
                'status'         => $request->status,
                'notes'          => $request->notes,
            ]);

            DB::commit();

            // Notify admins
            NotificationService::notifyAdmins(
                '📦 منتج جديد يحتاج مراجعة',
                "أضاف المورد {$supplier->company_name} منتجاً جديداً: {$product->name}. يحتاج إلى مراجعة.",
                route('admin.products.review', $product->id)
            );

            // Log activity
            activity('supplier_products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'action' => $request->action === 'new' ? 'created' : 'linked',
                ])
                ->log('✔ أضاف المورد منتجاً جديداً');

            return redirect()
                ->route('supplier.products.index')
                ->with('success', '✔ تم إضافة المنتج — بانتظار مراجعة الإدارة');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Supplier product creation error', [
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إضافة المنتج. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify supplier owns this product
        if (!$supplier->products()->where('products.id', $product->id)->exists()) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا المنتج');
        }

        $product->load(['category', 'manufacturer', 'suppliers']);

        // Get pivot data
        $pivot = $supplier->products()
            ->where('products.id', $product->id)
            ->first()
            ->pivot;

        // Get active categories with hierarchy (parent > child format)
        $categories = ProductCategory::active()
            ->with('parent')
            ->ordered()
            ->get()
            ->mapWithKeys(function ($category) {
                $displayName = $category->parent
                    ? $category->parent->name . ' > ' . $category->name
                    : $category->name;
                return [$category->id => $displayName];
            });

        $manufacturers = Manufacturer::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('supplier.products.edit', compact('product', 'pivot', 'categories', 'manufacturers'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param SupplierProductRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(SupplierProductRequest $request, Product $product): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify supplier owns this product
        if (!$supplier->products()->where('products.id', $product->id)->exists()) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا المنتج');
        }

        DB::beginTransaction();

        try {
            // Update base product data
            $product->update([
                'updated_by'   => Auth::id(),
                'name'         => $request->name,
                'model'        => $request->model,
                'brand'        => $request->brand,
                'manufacturer_id' => $request->manufacturer_id,
                'category_id'  => $request->category_id,
                'description'  => $request->description,
                'specifications' => $request->specifications
                    ? array_filter(array_map('trim', explode("\n", $request->specifications)))
                    : null,
                'features' => $request->features
                    ? array_filter(array_map('trim', explode("\n", $request->features)))
                    : null,
                'technical_data' => $request->technical_data
                    ? array_filter(array_map('trim', explode("\n", $request->technical_data)))
                    : null,
                'certifications' => $request->certifications
                    ? array_filter(array_map('trim', explode("\n", $request->certifications)))
                    : null,
                'installation_requirements' => $request->installation_requirements,
                // Reset review status on update
                'review_status'   => Product::REVIEW_PENDING,
                'review_notes'    => null,
                'rejection_reason'=> null,
            ]);

            // Update product images if provided
            if ($request->hasFile('images')) {
                $product->clearMediaCollection('product_images');
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('product_images');
                }
            }

            // Update supplier pivot data
            $supplier->products()->updateExistingPivot($product->id, [
                'price'          => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'lead_time'      => $request->lead_time,
                'warranty'       => $request->warranty,
                'status'         => $request->status,
                'notes'          => $request->notes,
            ]);

            DB::commit();

            // Notify admins
            NotificationService::notifyAdmins(
                '✏ منتج محدث يحتاج مراجعة',
                "قام المورد {$supplier->company_name} بتحديث منتج: {$product->name}. يحتاج إلى مراجعة.",
                route('admin.products.review', $product->id)
            );

            // Log activity
            activity('supplier_products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ])
                ->log('✏ حدّث المورد بيانات المنتج');

            return redirect()
                ->route('supplier.products.index')
                ->with('success', '✔ تم تحديث المنتج — بانتظار موافقة الإدارة');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Supplier product update error', [
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث المنتج. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Remove the specified product from the supplier's list.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $supplier = Auth::user()->supplierProfile;

            if (!$supplier) {
                abort(403, 'لا يوجد ملف تعريف للمورد');
            }

            // Verify supplier owns this product
            if (!$supplier->products()->where('products.id', $product->id)->exists()) {
                abort(403, 'ليس لديك صلاحية لحذف هذا المنتج');
            }

            // Detach product from supplier (removes pivot record only)
            $supplier->products()->detach($product->id);

            // Log activity
            activity('supplier_products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ])
                ->log('❌ أزال المورد المنتج من قائمته');

            return redirect()
                ->route('supplier.products.index')
                ->with('success', '❌ تم حذف المنتج من قائمتك (المنتج الأساسي مازال موجوداً)');

        } catch (\Throwable $e) {
            Log::error('Supplier product destroy error', [
                'product_id' => $product->id,
                'supplier_id' => Auth::user()->supplierProfile?->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف المنتج. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Display the specified product details for supplier.
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify supplier owns this product
        if (!$supplier->products()->where('products.id', $product->id)->exists()) {
            abort(403, 'ليس لديك صلاحية لعرض هذا المنتج');
        }

        $product->load(['category', 'manufacturer']);

        // Get pivot data
        $pivot = $supplier->products()
            ->where('products.id', $product->id)
            ->first()
            ->pivot;

        // Log activity
        activity('supplier_products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'review_status' => $product->review_status,
            ])
            ->log('عرض المورد تفاصيل المنتج: ' . $product->name);

        return view('supplier.products.show', compact('product', 'pivot'));
    }
}
