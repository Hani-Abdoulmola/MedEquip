<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * @package App\Http\Controllers\Web
 *
 * @author MedEquip System
 * @version 1.0.0
 *
 * @description Controller for managing product categories in the admin panel.
 *              Handles CRUD operations for categories and subcategories.
 */
class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = ProductCategory::with(['parent', 'children', 'products']);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by level (root or subcategory)
        if ($request->filled('level')) {
            if ($request->level === 'root') {
                $query->whereNull('parent_id');
            } elseif ($request->level === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        $categories = $query->ordered()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => ProductCategory::count(),
            'active'   => ProductCategory::active()->count(),
            'inactive' => ProductCategory::where('is_active', false)->count(),
            'roots'    => ProductCategory::roots()->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return View
     */
    public function create(): View
    {
        $parentCategories = ProductCategory::active()
            ->roots()
            ->with('children')
            ->ordered()
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150|unique:product_categories,name',
            'name_ar'     => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique'   => 'اسم الفئة موجود مسبقاً',
            'name.max'      => 'اسم الفئة يجب ألا يتجاوز 150 حرف',
            'parent_id.exists' => 'الفئة الأب غير موجودة',
        ]);

        DB::beginTransaction();
        try {
            $category = ProductCategory::create([
                'name'        => $validated['name'],
                'name_ar'     => $validated['name_ar'] ?? null,
                'description' => $validated['description'] ?? null,
                'parent_id'   => $validated['parent_id'] ?? null,
                'is_active'   => $request->boolean('is_active', true),
                'sort_order'  => $validated['sort_order'] ?? 0,
                'created_by'  => Auth::id(),
            ]);

            activity()
                ->performedOn($category)
                ->causedBy(Auth::user())
                ->log('تم إنشاء فئة منتجات جديدة: ' . $category->name);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', '✔ تم إضافة الفئة بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error storing product category: " . $e->getMessage(), ['exception' => $e]);
            return back()
                ->with('error', '❌ حدث خطأ أثناء إضافة الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified category.
     *
     * @param ProductCategory $category
     * @return View
     */
    public function show(ProductCategory $category): View
    {
        $category->load(['parent', 'children', 'products', 'creator', 'updater']);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param ProductCategory $category
     * @return View
     */
    public function edit(ProductCategory $category): View
    {
        $parentCategories = ProductCategory::active()
            ->roots()
            ->with('children')
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param Request $request
     * @param ProductCategory $category
     * @return RedirectResponse
     */
    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150|unique:product_categories,name,' . $category->id,
            'name_ar'     => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique'   => 'اسم الفئة موجود مسبقاً',
            'name.max'      => 'اسم الفئة يجب ألا يتجاوز 150 حرف',
            'parent_id.exists' => 'الفئة الأب غير موجودة',
        ]);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $category->id) {
            return back()
                ->with('error', '❌ لا يمكن تعيين الفئة كفئة أب لنفسها')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $category->update([
                'name'        => $validated['name'],
                'name_ar'     => $validated['name_ar'] ?? null,
                'description' => $validated['description'] ?? null,
                'parent_id'   => $validated['parent_id'] ?? null,
                'is_active'   => $request->boolean('is_active', true),
                'sort_order'  => $validated['sort_order'] ?? 0,
                'updated_by'  => Auth::id(),
            ]);

            activity()
                ->performedOn($category)
                ->causedBy(Auth::user())
                ->log('تم تحديث فئة المنتجات: ' . $category->name);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', '✔ تم تحديث الفئة بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error updating product category: " . $e->getMessage(), ['exception' => $e]);
            return back()
                ->with('error', '❌ حدث خطأ أثناء تحديث الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param ProductCategory $category
     * @return RedirectResponse
     */
    public function destroy(ProductCategory $category): RedirectResponse
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return back()->with('error', '❌ لا يمكن حذف الفئة لأنها تحتوي على منتجات. قم بحذف أو نقل المنتجات أولاً.');
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return back()->with('error', '❌ لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية. قم بحذف الفئات الفرعية أولاً.');
        }

        DB::beginTransaction();
        try {
            $categoryName = $category->name;
            $category->delete();

            activity()
                ->performedOn($category)
                ->causedBy(Auth::user())
                ->log('تم حذف فئة المنتجات: ' . $categoryName);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', '✔ تم حذف الفئة بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error deleting product category: " . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', '❌ حدث خطأ أثناء حذف الفئة: ' . $e->getMessage());
        }
    }
}

