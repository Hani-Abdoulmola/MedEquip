<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturerRequest;
use App\Models\Manufacturer;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin Manufacturer Controller
 *
 * Handles CRUD operations for manufacturers in the admin panel.
 */
class AdminManufacturerController extends Controller
{
    /**
     * Display a listing of manufacturers.
     */
    public function index(Request $request): View
    {
        $query = Manufacturer::with(['category', 'products']);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
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

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $manufacturers = $query->latest('id')
            ->paginate(20)
            ->withQueryString();

        // Calculate stats
        $stats = [
            'total' => Manufacturer::count(),
            'active' => Manufacturer::active()->count(),
            'inactive' => Manufacturer::where('is_active', false)->count(),
            'with_products' => Manufacturer::whereHas('products')->count(),
        ];

        // Get categories and countries for filters
        $categories = ProductCategory::active()->ordered()->get();
        $countries = Manufacturer::whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->sort()
            ->values();

        return view('admin.manufacturers.index', compact('manufacturers', 'stats', 'categories', 'countries'));
    }

    /**
     * Show the form for creating a new manufacturer.
     */
    public function create(): View
    {
        $categories = ProductCategory::active()->ordered()->get();

        return view('admin.manufacturers.create', compact('categories'));
    }

    /**
     * Store a newly created manufacturer in storage.
     */
    public function store(ManufacturerRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();

            $manufacturer = Manufacturer::create($data);

            activity()
                ->performedOn($manufacturer)
                ->causedBy(Auth::user())
                ->withProperties(['created_by' => Auth::id()])
                ->log('تم إنشاء شركة مصنعة جديدة: ' . $manufacturer->name);

            DB::commit();

            return redirect()
                ->route('admin.manufacturers.index')
                ->with('success', '✅ تم إضافة الشركة المصنعة بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Manufacturer store error: ' . $e->getMessage());

            return back()
                ->with('error', '❌ حدث خطأ أثناء إضافة الشركة المصنعة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified manufacturer.
     */
    public function show(Manufacturer $manufacturer): View
    {
        $manufacturer->load(['category', 'products']);

        return view('admin.manufacturers.show', compact('manufacturer'));
    }

    /**
     * Show the form for editing the specified manufacturer.
     */
    public function edit(Manufacturer $manufacturer): View
    {
        $categories = ProductCategory::active()->ordered()->get();

        return view('admin.manufacturers.edit', compact('manufacturer', 'categories'));
    }

    /**
     * Update the specified manufacturer in storage.
     */
    public function update(ManufacturerRequest $request, Manufacturer $manufacturer): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $manufacturer->update($data);

            activity()
                ->performedOn($manufacturer)
                ->causedBy(Auth::user())
                ->withProperties(['updated_by' => Auth::id()])
                ->log('تم تحديث الشركة المصنعة: ' . $manufacturer->name);

            DB::commit();

            return redirect()
                ->route('admin.manufacturers.index')
                ->with('success', '✅ تم تحديث الشركة المصنعة بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Manufacturer update error: ' . $e->getMessage());

            return back()
                ->with('error', '❌ حدث خطأ أثناء تحديث الشركة المصنعة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified manufacturer from storage.
     */
    public function destroy(Manufacturer $manufacturer): RedirectResponse
    {
        // Check if manufacturer has products
        if ($manufacturer->products()->exists()) {
            return back()->with('error', '❌ لا يمكن حذف الشركة المصنعة لأنها مرتبطة بمنتجات. قم بنقل المنتجات أولاً.');
        }

        DB::beginTransaction();

        try {
            $manufacturerName = $manufacturer->name;
            $manufacturer->delete();

            activity()
                ->performedOn($manufacturer)
                ->causedBy(Auth::user())
                ->log('تم حذف الشركة المصنعة: ' . $manufacturerName);

            DB::commit();

            return redirect()
                ->route('admin.manufacturers.index')
                ->with('success', '✅ تم حذف الشركة المصنعة بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Manufacturer delete error: ' . $e->getMessage());

            return back()->with('error', '❌ حدث خطأ أثناء حذف الشركة المصنعة: ' . $e->getMessage());
        }
    }
}

