<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Rfq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم المورد
     */
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // تحميل بروفايل المورد
        $user->load('supplierProfile');
        $supplier = $user->supplierProfile;

        if (! $supplier) {
            abort(403, 'Supplier profile not found');
        }

        // لو مرفوض أو قيد المراجعة → يرجع لصفحة الانتظار
        if ($supplier->rejection_reason) {
            return redirect()
                ->route('auth.waiting-approval')
                ->with('message', 'تم رفض طلب تسجيلك. يرجى مراجعة سبب الرفض مع الإدارة.');
        }

        if (! $supplier->is_verified) {
            return redirect()
                ->route('auth.waiting-approval')
                ->with('message', 'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة.');
        }

        // ========================
        // 📊 الإحصائيات الأساسية
        // ========================
        $totalProductsQuery = $supplier->products(); // belongsToMany

        $stats = [
            'total_products'      => $totalProductsQuery->count(),
            'pending_products'    => (clone $totalProductsQuery)->where('products.review_status', 'pending')->count(),
            'approved_products'   => (clone $totalProductsQuery)->where('products.review_status', 'approved')->count(),
            'needs_update_products' => (clone $totalProductsQuery)->where('products.review_status', 'needs_update')->count(),
            'rejected_products'   => (clone $totalProductsQuery)->where('products.review_status', 'rejected')->count(),

            'total_orders'        => Order::where('supplier_id', $supplier->id)->count(),
            'pending_orders'      => Order::where('supplier_id', $supplier->id)->where('status', Order::STATUS_PENDING)->count(),
            'completed_orders'    => Order::where('supplier_id', $supplier->id)->where('status', Order::STATUS_DELIVERED)->count(),

            'total_quotations'    => $supplier->quotations()->count(),
            'pending_quotations'  => $supplier->quotations()->where('status', 'pending')->count(),

            'total_revenue'       => Order::where('supplier_id', $supplier->id)
                                        ->where('status', Order::STATUS_DELIVERED)
                                        ->sum('total_amount'),

            'pending_rfqs'        => Rfq::availableFor($supplier->id)
                                        ->where('status', 'open')
                                        ->whereDoesntHave('quotations', fn($q) => $q->where('supplier_id', $supplier->id))
                                        ->count(),
            'total_rfqs'          => Rfq::availableFor($supplier->id)->count(),
        ];

        // ========================
        // 📈 بيانات الرسوم البيانية (ApexCharts)
        // ========================

        // 1) الإيرادات لآخر 6 أشهر
        $revenueRaw = Order::selectRaw('DATE_FORMAT(order_date, "%Y-%m") as ym, SUM(total_amount) as total')
            ->where('supplier_id', $supplier->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->where('order_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        $revenueLabels = [];
        $revenueData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $key       = $monthDate->format('Y-m');

            // اسم الشهر (ممكن نخليها إنجليزي عادي أو تضيف تعريب لو حبيت)
            $revenueLabels[] = $monthDate->format('M Y');
            $revenueData[]   = isset($revenueRaw[$key]) ? (float) $revenueRaw[$key] : 0;
        }

        // 2) حالة الطلبات (Donut)
        $ordersStatusRaw = Order::where('supplier_id', $supplier->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $orderStatusMap = [
            Order::STATUS_PENDING    => 'قيد المراجعة',
            Order::STATUS_PROCESSING => 'قيد التنفيذ',
            Order::STATUS_SHIPPED    => 'تم الشحن',
            Order::STATUS_DELIVERED  => 'تم التسليم',
            Order::STATUS_CANCELLED  => 'ملغي',
        ];

        $ordersStatusLabels = [];
        $ordersStatusData   = [];

        foreach ($ordersStatusRaw as $status => $count) {
            $ordersStatusLabels[] = $orderStatusMap[$status] ?? $status;
            $ordersStatusData[]   = (int) $count;
        }

        // 3) حالة مراجعة المنتجات (Donut)
        $productReviewRaw = [
            'pending'      => (clone $totalProductsQuery)->where('products.review_status', 'pending')->count(),
            'approved'     => (clone $totalProductsQuery)->where('products.review_status', 'approved')->count(),
            'needs_update' => (clone $totalProductsQuery)->where('products.review_status', 'needs_update')->count(),
            'rejected'     => (clone $totalProductsQuery)->where('products.review_status', 'rejected')->count(),
        ];

        $productReviewMap = [
            'pending'      => 'قيد المراجعة',
            'approved'     => 'معتمد',
            'needs_update' => 'يحتاج تعديل',
            'rejected'     => 'مرفوض',
        ];

        $productReviewLabels = [];
        $productReviewData   = [];

        foreach ($productReviewRaw as $key => $count) {
            $productReviewLabels[] = $productReviewMap[$key] ?? $key;
            $productReviewData[]   = (int) $count;
        }

        $charts = [
            'revenue' => [
                'labels' => $revenueLabels,
                'data'   => $revenueData,
            ],
            'orders_status' => [
                'labels' => $ordersStatusLabels,
                'data'   => $ordersStatusData,
            ],
            'products_review' => [
                'labels' => $productReviewLabels,
                'data'   => $productReviewData,
            ],
        ];

        // ========================
        // 🕒 آخر الطلبات
        // ========================
        $recentOrders = Order::where('supplier_id', $supplier->id)
            ->with('buyer')
            ->latest('order_date')
            ->limit(5)
            ->get();

        return view('supplier.dashboard', [
            'supplier'     => $supplier,
            'stats'        => $stats,
            'charts'       => $charts,
            'recentOrders' => $recentOrders,
        ]);
    }
}
