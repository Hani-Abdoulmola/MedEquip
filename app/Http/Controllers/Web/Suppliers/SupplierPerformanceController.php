<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\SupplierPerformanceMetric;
use App\Services\SupplierPerformanceService;
use Illuminate\View\View;

class SupplierPerformanceController extends Controller
{
    protected SupplierPerformanceService $performanceService;

    public function __construct(SupplierPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Display supplier performance dashboard.
     */
    public function index(): View
    {
        $supplier = auth()->user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يمكن الوصول إلى هذه الصفحة');
        }

        $dashboard = $this->performanceService->getSupplierDashboard($supplier);

        // Get last 6 months metrics for trends
        $monthlyMetrics = SupplierPerformanceMetric::where('supplier_id', $supplier->id)
            ->whereBetween('period_start', [now()->subMonths(6), now()])
            ->orderBy('period_start', 'asc')
            ->get();

        return view('supplier.performance.index', compact('dashboard', 'monthlyMetrics'));
    }

    /**
     * Display detailed metrics for a specific period.
     */
    public function show(SupplierPerformanceMetric $metric): View
    {
        $supplier = auth()->user()->supplierProfile;

        if ($metric->supplier_id !== $supplier->id) {
            abort(403);
        }

        $metric->load('supplier');

        return view('supplier.performance.show', compact('metric'));
    }
}
