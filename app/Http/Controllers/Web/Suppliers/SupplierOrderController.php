<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Exports\SupplierOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Supplier Order Controller
 *
 * Handles order management for suppliers.
 * Suppliers can view their orders, update status, and manage deliveries.
 */
class SupplierOrderController extends Controller
{
    /**
     * Display list of orders for the supplier.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Order::with(['buyer', 'items', 'quotation'])
            ->where('supplier_id', $supplier->id);

        // Filter by status (supports multiple statuses)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Enhanced search across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($sub) use ($search) {
                      $sub->where('organization_name', 'like', "%{$search}%")
                          ->orWhere('contact_email', 'like', "%{$search}%")
                          ->orWhere('contact_phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('quotation', fn($sub) => $sub->where('reference_code', 'like', "%{$search}%"));
            });
        }

        // Date range filter with quick filters
        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            match ($dateFilter) {
                'today' => $query->whereDate('order_date', today()),
                'this_week' => $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year),
                'last_month' => $query->whereMonth('order_date', now()->subMonth()->month)->whereYear('order_date', now()->subMonth()->year),
                default => null,
            };
        } else {
            // Custom date range
            if ($request->filled('date_from')) {
                $query->whereDate('order_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('order_date', '<=', $request->date_to);
            }
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }

        // Currency filter
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $orders = $query->latest('order_date')->paginate(15)->withQueryString();

        // Optimized stats calculation using single query
        $stats = Order::where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled,
                COALESCE(SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END), 0) as total_revenue
            ', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
                Order::STATUS_DELIVERED,
            ])
            ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'processing' => $stats->processing ?? 0,
            'shipped' => $stats->shipped ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
        ];

        // Log activity
        activity('supplier_orders')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
            ])
            ->log('عرض المورد قائمة الطلبات');

        return view('supplier.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details.
     */
    public function show(Order $order): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify ownership
        if ($order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا الطلب');
        }

        $order->load(['buyer.user', 'items.product', 'quotation', 'invoices', 'deliveries']);

        // Log activity
        activity('supplier_orders')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
            ])
            ->log('عرض المورد تفاصيل الطلب: ' . $order->order_number);

        return view('supplier.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify ownership
        if ($order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتحديث هذا الطلب');
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ]),
            'notes' => 'nullable|string|max:500',
        ]);

        // Validate status transition
        $allowedTransitions = [
            Order::STATUS_PENDING => [Order::STATUS_PROCESSING, Order::STATUS_CANCELLED],
            Order::STATUS_PROCESSING => [Order::STATUS_SHIPPED, Order::STATUS_CANCELLED],
            Order::STATUS_SHIPPED => [Order::STATUS_DELIVERED],
            Order::STATUS_DELIVERED => [], // Final state
            Order::STATUS_CANCELLED => [], // Final state
        ];

        $currentStatus = $order->status;
        $newStatus = $request->status;

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            return back()->withErrors(['status' => 'لا يمكن تغيير الحالة من "' . $this->getStatusLabel($currentStatus) . '" إلى "' . $this->getStatusLabel($newStatus) . '"']);
        }

        DB::beginTransaction();

        try {
            $oldStatus = $order->status;

            $order->update([
                'status' => $newStatus,
                'notes' => $request->notes ?? $order->notes,
            ]);

            // Notify buyer of status change
            if ($order->buyer && $order->buyer->user) {
                $statusLabels = [
                    Order::STATUS_PROCESSING => 'قيد المعالجة',
                    Order::STATUS_SHIPPED => 'تم الشحن',
                    Order::STATUS_DELIVERED => 'تم التسليم',
                    Order::STATUS_CANCELLED => 'ملغى',
                ];

                $statusLabel = $statusLabels[$newStatus] ?? $newStatus;
                NotificationService::send(
                    $order->buyer->user,
                    '🔄 تحديث حالة الطلب',
                    "تم تحديث حالة طلبك رقم {$order->order_number} إلى: {$statusLabel}",
                    route('supplier.orders.show', $order)
                );
            }

            // Notify admin
            NotificationService::notifyAdmins(
                '🔄 تحديث حالة طلب',
                "قام المورد {$supplier->company_name} بتحديث حالة الطلب رقم {$order->order_number} من {$this->getStatusLabel($oldStatus)} إلى {$this->getStatusLabel($newStatus)}",
                route('supplier.orders.show', $order)
            );

            // Log activity
            activity('supplier_orders')
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'notes' => $request->notes ?? null,
                ])
                ->log("قام المورد بتحديث حالة الطلب من {$this->getStatusLabel($oldStatus)} إلى {$this->getStatusLabel($newStatus)}");

            DB::commit();

            return redirect()
                ->route('supplier.orders.show', $order)
                ->with('success', 'تم تحديث حالة الطلب بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order status update error', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث حالة الطلب']);
        }
    }

    /**
     * Export orders to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $filters = $request->only(['status', 'from_date', 'to_date']);

        // Log activity
        activity('supplier_orders')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'action' => 'export',
                'filters' => $filters,
            ])
            ->log('قام المورد بتصدير قائمة الطلبات');

        $fileName = 'orders-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new SupplierOrdersExport($supplier->id, $filters), $fileName);
    }

    /**
     * Get status label in Arabic.
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            Order::STATUS_PENDING => 'قيد الانتظار',
            Order::STATUS_PROCESSING => 'قيد المعالجة',
            Order::STATUS_SHIPPED => 'تم الشحن',
            Order::STATUS_DELIVERED => 'تم التسليم',
            Order::STATUS_CANCELLED => 'ملغى',
            default => $status,
        };
    }
}

