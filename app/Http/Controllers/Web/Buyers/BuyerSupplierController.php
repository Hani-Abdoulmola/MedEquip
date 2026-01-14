<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Buyer Supplier Controller
 * 
 * Handles supplier directory browsing for buyers.
 * Buyers can view verified suppliers, see their products,
 * ratings, and initiate contact or RFQs.
 */
class BuyerSupplierController extends Controller
{
    /**
     * Display the verified suppliers directory.
     */
    public function index(Request $request): View
    {
        $buyer = Auth::user()->buyerProfile;

        $query = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->with(['user', 'products' => function ($q) {
                $q->where('is_active', true)
                  ->where('review_status', 'approved')
                  ->limit(4);
            }])
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true)
                  ->where('review_status', 'approved');
            }])
            ->withCount(['quotations' => function ($q) {
                $q->where('status', 'accepted');
            }])
            ->withCount('orders');

        // Search by company name or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by location (city)
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Filter by category (suppliers who have products in this category)
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->whereHas('products', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->where('is_active', true)
                  ->where('review_status', 'approved');
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'products_count');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('company_name');
                break;
            case 'orders':
                $query->orderBy('orders_count', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'products_count':
            default:
                $query->orderBy('products_count', 'desc');
                break;
        }

        $suppliers = $query->paginate(12)->withQueryString();

        // Get unique cities for filter
        $cities = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        // Get categories for filter
        $categories = ProductCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        // Stats
        $stats = [
            'total_suppliers' => Supplier::where('is_verified', true)->where('is_active', true)->count(),
            'total_products' => \App\Models\Product::where('is_active', true)
                ->where('review_status', 'approved')
                ->whereHas('suppliers', function ($q) {
                    $q->where('is_verified', true)->where('is_active', true);
                })->count(),
        ];

        return view('buyer.suppliers.index', compact(
            'suppliers',
            'cities',
            'categories',
            'stats'
        ));
    }

    /**
     * Display supplier details with their products.
     */
    public function show(Supplier $supplier): View
    {
        // Only show verified and active suppliers
        if (!$supplier->is_verified || !$supplier->is_active) {
            abort(404, 'المورد غير متوفر');
        }

        $supplier->load([
            'user',
            'products' => function ($q) {
                $q->where('is_active', true)
                  ->where('review_status', 'approved')
                  ->with(['category', 'media'])
                  ->withPivot(['price', 'stock_quantity', 'lead_time', 'warranty', 'status'])
                  ->wherePivot('status', 'available');
            }
        ]);

        // Get supplier statistics
        $stats = [
            'products_count' => $supplier->products()
                ->where('is_active', true)
                ->where('review_status', 'approved')
                ->count(),
            'accepted_quotations' => $supplier->quotations()
                ->where('status', 'accepted')
                ->count(),
            'completed_orders' => $supplier->orders()
                ->where('status', 'delivered')
                ->count(),
            'member_since' => $supplier->created_at->format('Y'),
        ];

        // Get categories of supplier's products
        $productCategories = ProductCategory::whereHas('products', function ($q) use ($supplier) {
            $q->whereHas('suppliers', function ($sq) use ($supplier) {
                $sq->where('suppliers.id', $supplier->id);
            })
            ->where('is_active', true)
            ->where('review_status', 'approved');
        })->get();

        // Paginate products
        $products = $supplier->products()
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->wherePivot('status', 'available')
            ->with(['category', 'media'])
            ->paginate(12);

        // Get approved reviews for this supplier
        $reviews = $supplier->approvedReviews()
            ->with(['buyer', 'order'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('buyer.suppliers.show', compact(
            'supplier',
            'stats',
            'productCategories',
            'products',
            'reviews'
        ));
    }
}

