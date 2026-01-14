<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\SupplierProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
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
 * SIMPLIFIED WORKFLOW:
 * - Suppliers can create NEW products (pending admin review)
 * - Suppliers can link to EXISTING approved products
 * - Suppliers manage their offer data (price, stock, etc.)
 */
class SupplierProductController extends Controller
{
    /**
     * Display a listing of the supplier's products.
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

        // Optimized stats calculation
        $statsQuery = Product::query()
            ->join('product_supplier', 'products.id', '=', 'product_supplier.product_id')
            ->where('product_supplier.supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN products.review_status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN products.review_status = ? THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN products.review_status = ? THEN 1 ELSE 0 END) as needs_update,
                SUM(CASE WHEN products.review_status = ? THEN 1 ELSE 0 END) as rejected
            ', [
                Product::REVIEW_PENDING,
                Product::REVIEW_APPROVED,
                Product::REVIEW_NEEDS_UPDATE,
                Product::REVIEW_REJECTED,
            ])
            ->first();

        $stats = [
            'total'        => $statsQuery->total ?? 0,
            'pending'      => $statsQuery->pending ?? 0,
            'approved'     => $statsQuery->approved ?? 0,
            'needs_update' => $statsQuery->needs_update ?? 0,
            'rejected'     => $statsQuery->rejected ?? 0,
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
     */
    public function create(): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Get existing products not yet linked to this supplier
        // Shows approved products, OR pending products created by other suppliers
        $existingProducts = Product::where('is_active', true)
            ->where(function ($q) {
                $q->where('review_status', Product::REVIEW_APPROVED)
                  ->orWhere('review_status', Product::REVIEW_PENDING);
            })
            ->whereDoesntHave('suppliers', fn($q) => $q->where('suppliers.id', $supplier->id))
            ->with(['category:id,name', 'manufacturer:id,name', 'media'])
            ->orderBy('name')
            ->get();

        // Get active categories with hierarchy
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
     * WORKFLOW:
     * - action='new': Creates a new product (pending admin review)
     * - action='existing': Links supplier to existing approved product
     */
    public function store(SupplierProductRequest $request): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        DB::beginTransaction();

        try {
            if ($request->action === 'new') {
                // CREATE NEW PRODUCT (Pending admin review)
                $product = Product::create([
                    'created_by' => Auth::id(),
                    'name' => $request->name,
                    'model' => $request->model,
                    'brand' => $request->brand,
                    'manufacturer_id' => $request->manufacturer_id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
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
                    'is_active' => true,
                    'review_status' => Product::REVIEW_PENDING, // Needs admin approval
                ]);

                // Upload images if provided
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $product->addMedia($image)->toMediaCollection('product_images');
                    }
                }

                // Link supplier with offer data
                $supplier->products()->attach($product->id, [
                    'price' => $request->price,
                    'stock_quantity' => $request->stock_quantity,
                    'lead_time' => $request->lead_time,
                    'warranty' => $request->warranty,
                    'status' => $request->status,
                    'notes' => $request->notes,
                ]);

                DB::commit();

                // Log activity
                activity('supplier_products')
                    ->performedOn($product)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'action' => 'created_new',
                    ])
                    ->log('📦 أنشأ المورد منتجاً جديداً (بانتظار المراجعة)');

                return redirect()
                    ->route('supplier.products.index')
                    ->with('success', '✔ تم إضافة المنتج بنجاح — بانتظار مراجعة الإدارة');

            } else {
                // LINK EXISTING PRODUCT
                $product = Product::where('is_active', true)
                    ->whereIn('review_status', [Product::REVIEW_APPROVED, Product::REVIEW_PENDING])
                    ->findOrFail($request->product_id);

                // Check if already linked
                if ($supplier->products()->where('products.id', $product->id)->exists()) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->withErrors(['product_id' => 'هذا المنتج مرتبط بك مسبقاً.']);
                }

                // Attach supplier with offer data
                $supplier->products()->attach($product->id, [
                    'price' => $request->price,
                    'stock_quantity' => $request->stock_quantity,
                    'lead_time' => $request->lead_time,
                    'warranty' => $request->warranty,
                    'status' => $request->status,
                    'notes' => $request->notes,
                ]);

                DB::commit();

                // Log activity
                activity('supplier_products')
                    ->performedOn($product)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'action' => 'linked_existing',
                    ])
                    ->log('🔗 ربط المورد منتجاً من الكتالوج');

                return redirect()
                    ->route('supplier.products.index')
                    ->with('success', "✔ تم ربط المنتج: {$product->name}");
            }

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Supplier product creation error', [
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified product.
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

        // Get active categories with hierarchy
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
     * Suppliers can update:
     * - Their offer data (price, stock, etc.) - ALWAYS
     * - Product data (name, specs, etc.) - ONLY if review_status is 'needs_update'
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
            // Update offer data (pivot) - ALWAYS allowed
            $supplier->products()->updateExistingPivot($product->id, [
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'lead_time' => $request->lead_time,
                'warranty' => $request->warranty,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Update product data ONLY if needs_update
            if ($product->review_status === Product::REVIEW_NEEDS_UPDATE) {
                $product->update([
                    'name' => $request->name,
                    'model' => $request->model,
                    'brand' => $request->brand,
                    'manufacturer_id' => $request->manufacturer_id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
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
                    'review_status' => Product::REVIEW_PENDING, // Reset to pending for re-review
                ]);

                // Handle new images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $product->addMedia($image)->toMediaCollection('product_images');
                    }
                }
            }

            DB::commit();

            // Log activity
            activity('supplier_products')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ])
                ->log('🔄 حدّث المورد بيانات المنتج');

            return redirect()
                ->route('supplier.products.index')
                ->with('success', '✔ تم تحديث المنتج بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Supplier product update error', [
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified product from the supplier's list.
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
                ->with('success', '❌ تم حذف المنتج من قائمتك');

        } catch (\Throwable $e) {
            Log::error('Supplier product destroy error', [
                'product_id' => $product->id,
                'supplier_id' => Auth::user()->supplierProfile?->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف المنتج.']);
        }
    }

    /**
     * Display the specified product details.
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
            ])
            ->log('عرض المورد تفاصيل المنتج: ' . $product->name);

        return view('supplier.products.show', compact('product', 'pivot'));
    }
}
