<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDiagnosticsController extends Controller
{
    /**
     * Display factory data diagnostic page.
     */
    public function factoryData(): View
    {
        // Permission check is handled by route middleware

        // Get all statistics
        $stats = [
            'categories' => [
                'total' => ProductCategory::count(),
                'active' => ProductCategory::where('is_active', true)->count(),
                'with_products' => ProductCategory::whereHas('products')->count(),
            ],
            'manufacturers' => [
                'total' => Manufacturer::count(),
                'active' => Manufacturer::where('is_active', true)->count(),
                'with_products' => Manufacturer::whereHas('products')->count(),
            ],
            'suppliers' => [
                'total' => Supplier::count(),
                'verified' => Supplier::where('is_verified', true)->count(),
                'active' => Supplier::where('is_active', true)->count(),
                'with_products' => Supplier::whereHas('products')->count(),
                'with_users' => Supplier::whereNotNull('user_id')->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('is_active', true)->count(),
                'approved' => Product::where('review_status', Product::REVIEW_APPROVED)->count(),
                'with_category' => Product::whereNotNull('category_id')->count(),
                'with_manufacturer' => Product::whereNotNull('manufacturer_id')->count(),
                'with_suppliers' => Product::whereHas('suppliers')->count(),
            ],
            'relationships' => [
                'total_offers' => DB::table('product_supplier')->count(),
                'available_offers' => DB::table('product_supplier')->where('status', 'available')->count(),
            ],
        ];

        // Get sample data
        $samples = [
            'suppliers' => Supplier::take(5)->get(),
            'products' => Product::take(5)->get(),
            'categories' => ProductCategory::where('is_active', true)->take(5)->get(),
            'manufacturers' => Manufacturer::where('is_active', true)->take(5)->get(),
        ];

        return view('admin.diagnostics.factory-data', compact('stats', 'samples'));
    }
}
