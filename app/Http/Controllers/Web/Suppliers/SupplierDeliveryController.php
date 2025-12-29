<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\SupplierDeliveryProofRequest;
use App\Http\Requests\Suppliers\SupplierDeliveryRequest;
use App\Http\Requests\Suppliers\SupplierDeliveryStatusRequest;
use App\Models\Delivery;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Supplier Delivery Controller
 *
 * Handles delivery management for suppliers.
 */
class SupplierDeliveryController extends Controller
{
    /**
     * Display list of deliveries for the supplier.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Delivery::with(['order', 'buyer'])
            ->where('supplier_id', $supplier->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('delivery_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('delivery_date', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"));
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15)->withQueryString();

        // Optimized stats calculation
        $stats = Delivery::where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
            ', [
                Delivery::STATUS_PENDING,
                Delivery::STATUS_IN_TRANSIT,
                Delivery::STATUS_DELIVERED,
                Delivery::STATUS_FAILED,
            ])
            ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'in_transit' => $stats->in_transit ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'failed' => $stats->failed ?? 0,
        ];

        return view('supplier.deliveries.index', compact('deliveries', 'stats'));
    }

    /**
     * Display delivery details.
     */
    public function show(Delivery $delivery): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier || $delivery->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا التسليم');
        }

        $delivery->load(['order.items.product', 'buyer', 'creator', 'verifier']);

        return view('supplier.deliveries.show', compact('delivery'));
    }

    /**
     * Show form to create a delivery for an order.
     */
    public function create(Order $order): View|RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لإنشاء تسليم لهذا الطلب');
        }

        // Check if order is shipped
        if ($order->status !== Order::STATUS_SHIPPED) {
            return redirect()
                ->route('supplier.orders.show', $order)
                ->with('error', 'لا يمكن إنشاء تسليم إلا للطلبات المشحونة');
        }

        // Check if delivery already exists
        if ($order->delivery) {
            return redirect()
                ->route('supplier.deliveries.show', $order->delivery)
                ->with('info', 'يوجد سجل تسليم لهذا الطلب');
        }

        $order->load(['buyer', 'items.product']);

        return view('supplier.deliveries.create', compact('order'));
    }

    /**
     * Store a new delivery.
     */
    public function store(SupplierDeliveryRequest $request, Order $order): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لإنشاء تسليم لهذا الطلب');
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $delivery = Delivery::create([
                'order_id' => $order->id,
                'supplier_id' => $supplier->id,
                'buyer_id' => $order->buyer_id,
                'created_by' => Auth::id(),
                'delivery_number' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_DELIVERY,
                    Delivery::class,
                    'delivery_number'
                ),
                'delivery_date' => $validated['delivery_date'],
                'status' => Delivery::STATUS_IN_TRANSIT,
                'delivery_location' => $validated['delivery_location'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Notify admin
            NotificationService::notifyAdmins(
                '🚚 تسليم جديد',
                "تم إنشاء سجل تسليم جديد رقم {$delivery->delivery_number} من المورد {$supplier->company_name} للطلب: {$order->order_number}",
                route('supplier.deliveries.show', $delivery)
            );

            // Notify buyer
            if ($order->buyer && $order->buyer->user) {
                NotificationService::send(
                    $order->buyer->user,
                    '📦 تم إنشاء سجل تسليم',
                    "تم إنشاء سجل تسليم لطلبك رقم {$order->order_number} من المورد {$supplier->company_name}",
                    route('supplier.deliveries.show', $delivery)
                );
            }

            // Log activity
            activity('supplier_deliveries')
                ->performedOn($delivery)
                ->causedBy(Auth::user())
                ->withProperties([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'delivery_number' => $delivery->delivery_number,
                    'delivery_date' => $delivery->delivery_date,
                    'status' => $delivery->status,
                ])
                ->log('أنشأ المورد سجل تسليم جديد: ' . $delivery->delivery_number);

            DB::commit();

            return redirect()
                ->route('supplier.deliveries.show', $delivery)
                ->with('success', 'تم إنشاء سجل التسليم بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Delivery creation error', [
                'supplier_id' => $supplier->id,
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء سجل التسليم. يرجى التحقق من البيانات والمحاولة مرة أخرى.']);
        }
    }

    /**
     * Update delivery status.
     */
    public function updateStatus(SupplierDeliveryStatusRequest $request, Delivery $delivery): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier || $delivery->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتحديث هذا التسليم');
        }

        $validated = $request->validated();
        $oldStatus = $delivery->status;
        $newStatus = $validated['status'];

        DB::beginTransaction();

        try {
            $delivery->update(['status' => $newStatus]);

            // If delivered, update order status
            if ($newStatus === Delivery::STATUS_DELIVERED) {
                $delivery->order->update(['status' => Order::STATUS_DELIVERED]);

                // Notify buyer
                if ($delivery->buyer && $delivery->buyer->user) {
                    NotificationService::send(
                        $delivery->buyer->user,
                        '✅ تم تأكيد التسليم',
                        "تم تأكيد تسليم طلبك رقم {$delivery->order->order_number} بنجاح.",
                        route('supplier.deliveries.show', $delivery)
                    );
                }

                // Notify admin
                NotificationService::notifyAdmins(
                    '✅ تأكيد تسليم مكتمل',
                    "تم تأكيد تسليم رقم {$delivery->delivery_number} بنجاح.",
                    route('supplier.deliveries.show', $delivery)
                );
            }

            // Notify on status change
            if ($oldStatus !== $newStatus) {
                // Notify buyer of status update
                if ($delivery->buyer && $delivery->buyer->user && $newStatus !== Delivery::STATUS_DELIVERED) {
                    $statusLabels = [
                        Delivery::STATUS_PENDING => 'قيد الانتظار',
                        Delivery::STATUS_IN_TRANSIT => 'قيد النقل',
                        Delivery::STATUS_DELIVERED => 'تم التسليم',
                        Delivery::STATUS_FAILED => 'فشل التسليم',
                    ];

                    NotificationService::send(
                        $delivery->buyer->user,
                        '🔄 تحديث حالة التسليم',
                        "تم تحديث حالة تسليم طلبك رقم {$delivery->order->order_number} إلى: {$statusLabels[$newStatus]}",
                        route('supplier.deliveries.show', $delivery)
                    );
                }
            }

            // Log activity
            activity('supplier_deliveries')
                ->performedOn($delivery)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'delivery_number' => $delivery->delivery_number,
                    'order_id' => $delivery->order_id,
                    'order_number' => $delivery->order->order_number,
                ])
                ->log("حدّث المورد حالة التسليم من {$oldStatus} إلى {$newStatus}");

            DB::commit();

            return back()->with('success', 'تم تحديث حالة التسليم بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Delivery status update error', [
                'delivery_id' => $delivery->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث حالة التسليم']);
        }
    }

    /**
     * Upload delivery proof.
     */
    public function uploadProof(SupplierDeliveryProofRequest $request, Delivery $delivery): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier || $delivery->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتحديث هذا التسليم');
        }

        try {
            $delivery->addMediaFromRequest('proof')
                ->toMediaCollection('delivery_proofs');

            // Notify admin
            NotificationService::notifyAdmins(
                '📎 إثبات تسليم جديد',
                "تم رفع إثبات تسليم جديد من المورد {$supplier->company_name} للتسليم رقم {$delivery->delivery_number}",
                route('supplier.deliveries.show', $delivery)
            );

            // Log activity
            activity('supplier_deliveries')
                ->performedOn($delivery)
                ->causedBy(Auth::user())
                ->withProperties([
                    'delivery_number' => $delivery->delivery_number,
                    'order_id' => $delivery->order_id,
                ])
                ->log('رفع المورد إثبات تسليم: ' . $delivery->delivery_number);

            return back()->with('success', 'تم رفع إثبات التسليم بنجاح');

        } catch (\Throwable $e) {
            Log::error('Delivery proof upload error', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['proof' => 'حدث خطأ أثناء رفع الملف. يرجى التأكد من صحة الملف والمحاولة مرة أخرى.']);
        }
    }
}

