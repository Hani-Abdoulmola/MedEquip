<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Buyer;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Exports\AdminOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📦 قائمة أوامر الشراء (Role-Based View)
     */
    public function index(): View
    {
        $user = auth()->user();
        $query = Order::with(['quotation.rfq', 'buyer', 'supplier', 'items']);

        // Role-based filtering
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Buyers only see their own orders
            $query->where('buyer_id', $user->buyerProfile->id);
        } elseif ($user->hasRole('Supplier') && $user->supplierProfile) {
            // Suppliers only see orders assigned to them
            $query->where('supplier_id', $user->supplierProfile->id);
        }
        // Admins see all orders (no filter)

        // Filters
        if (request()->filled('buyer') && $user->hasRole('Admin')) {
            $query->where('buyer_id', request('buyer'));
        }

        if (request()->filled('supplier') && $user->hasRole('Admin')) {
            $query->where('supplier_id', request('supplier'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('buyer', function ($q) use ($search) {
                        $q->where('organization_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest('id')->paginate(15);

        // Role-based stats calculation
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            $buyerId = $user->buyerProfile->id;
            $stats = [
                'total_orders' => Order::where('buyer_id', $buyerId)->count(),
                'pending_orders' => Order::where('buyer_id', $buyerId)->where('status', 'pending')->count(),
                'processing_orders' => Order::where('buyer_id', $buyerId)->where('status', 'processing')->count(),
                'delivered_orders' => Order::where('buyer_id', $buyerId)->where('status', 'delivered')->count(),
            ];
        } elseif ($user->hasRole('Supplier') && $user->supplierProfile) {
            $supplierId = $user->supplierProfile->id;
            $stats = [
                'total_orders' => Order::where('supplier_id', $supplierId)->count(),
                'pending_orders' => Order::where('supplier_id', $supplierId)->where('status', 'pending')->count(),
                'processing_orders' => Order::where('supplier_id', $supplierId)->where('status', 'processing')->count(),
                'delivered_orders' => Order::where('supplier_id', $supplierId)->where('status', 'delivered')->count(),
            ];
        } else {
            // Admin stats (all orders)
            $stats = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'delivered_orders' => Order::where('status', 'delivered')->count(),
            ];
        }

        // Dynamic view selection
        if ($user->hasRole('Admin')) {
            $view = 'admin.orders.index';
            $buyers = Buyer::pluck('organization_name', 'id');
            $suppliers = Supplier::pluck('company_name', 'id');
            return view($view, compact('orders', 'stats', 'buyers', 'suppliers'));
        } elseif ($user->hasRole('Buyer')) {
            $view = 'supplier.orders.index'; // Reuse supplier view for now
            return view($view, compact('orders', 'stats'));
        } else {
            // Supplier view
            $view = 'supplier.orders.index';
            return view($view, compact('orders', 'stats'));
        }
    }

    /**
     * ➕ إنشاء أمر شراء جديد
     */
    public function create(): View
    {
        $user = auth()->user();

        // Role-based data filtering
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Buyers can only create orders from their accepted quotations
            $quotations = \App\Models\Quotation::where('status', 'accepted')
                ->where('buyer_id', $user->buyerProfile->id)
                ->pluck('reference_code', 'id');
            $suppliers = Supplier::pluck('company_name', 'id');

            $view = 'supplier.orders.create'; // Reuse supplier view for now
            return view($view, [
                'order' => new Order,
                'quotations' => $quotations,
                'suppliers' => $suppliers,
            ]);
        } else {
            // Admin view
            $quotations = \App\Models\Quotation::where('status', 'accepted')->pluck('reference_code', 'id');
            $buyers = Buyer::pluck('organization_name', 'id');
            $suppliers = Supplier::pluck('company_name', 'id');

            $view = 'admin.orders.create';
            return view($view, [
                'order' => new Order,
                'quotations' => $quotations,
                'buyers' => $buyers,
                'suppliers' => $suppliers,
            ]);
        }
    }

    /**
     * 💾 تخزين أمر شراء جديد (Buyer Role)
     */
    public function store(OrderRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['order_number'] = \App\Services\ReferenceCodeService::generateUnique(
                \App\Services\ReferenceCodeService::PREFIX_ORDER,
                \App\Models\Order::class,
                'order_number'
            );
            $data['created_by'] = Auth::id();

            $order = Order::create($data);

            // 🔔 إشعارات تلقائية
            NotificationService::notifyAdmins(
                '📦 طلب شراء جديد',
                "تم إنشاء أمر شراء رقم {$order->order_number} بين {$order->buyer->organization_name} و{$order->supplier->company_name}.",
                route('admin.orders.show', $order->id)
            );

            // Send notification to buyer
            if ($order->buyer && $order->buyer->user) {
                $buyerRoute = auth()->user()->hasRole('Buyer') 
                    ? 'buyer.orders' 
                    : 'admin.orders.show';
                NotificationService::send(
                    $order->buyer->user,
                    '🛒 تم إنشاء طلبك بنجاح',
                    "تم إنشاء الطلب رقم {$order->order_number}. يمكنك متابعة حالته من لوحة التحكم.",
                    route($buyerRoute, $order->id)
                );
            }

            // Send notification to supplier
            if ($order->supplier && $order->supplier->user) {
                $supplierRoute = auth()->user()->hasRole('Supplier') 
                    ? 'supplier.orders.show' 
                    : 'admin.orders.show';
                NotificationService::send(
                    $order->supplier->user,
                    '📦 طلب جديد من مشتري',
                    "تم إرسال طلب جديد من {$order->buyer->organization_name}.",
                    route($supplierRoute, $order->id)
                );
            }

            // 🧾 تسجيل النشاط
            activity()
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties([
                    'created_by' => Auth::id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('📦 تم إنشاء أمر شراء جديد');

            DB::commit();

            // Dynamic redirect based on user role
            $user = auth()->user();
            $redirectRoute = $user->hasRole('Admin') 
                ? 'admin.orders' 
                : ($user->hasRole('Buyer') 
                    ? 'buyer.orders' 
                    : 'supplier.orders.index');

            return redirect()
                ->route($redirectRoute)
                ->with('success', '✅ تم إنشاء أمر الشراء بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل إنشاء أمر الشراء: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل أمر شراء (Admin Only - Status & Notes)
     */
    public function edit(Order $order): View
    {
        $user = auth()->user();

        // Authorization check - Only admins can edit orders
        if (!$user->hasRole('Admin')) {
            abort(403, 'غير مصرح لك بتعديل الطلبات');
        }

        $order->load(['quotation.rfq', 'buyer', 'supplier', 'items']);

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * 🔄 تحديث بيانات أمر الشراء (Admin Only - Status & Notes)
     */
    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $oldStatus = $order->status;

            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $order->update($data);

            // 🧾 سجل النشاط
            activity()
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties([
                    'updated_by' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $order->status,
                ])
                ->log('📦 تم تحديث أمر الشراء');

            // 🔔 إشعارات حالة الطلب (only if status changed)
            if ($oldStatus !== $order->status) {
                switch ($order->status) {
                    case 'processing':
                        if ($order->buyer && $order->buyer->user) {
                            $buyerRoute = $order->buyer->user->hasRole('Buyer') 
                                ? 'buyer.orders' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->buyer->user,
                                '🔄 جاري تجهيز طلبك',
                                "طلبك رقم {$order->order_number} الآن قيد التجهيز.",
                                route($buyerRoute, $order->id)
                            );
                        }
                        if ($order->supplier && $order->supplier->user) {
                            $supplierRoute = $order->supplier->user->hasRole('Supplier') 
                                ? 'supplier.orders.show' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->supplier->user,
                                '🔄 طلب قيد التجهيز',
                                "الطلب رقم {$order->order_number} الآن قيد التجهيز.",
                                route($supplierRoute, $order->id)
                            );
                        }
                        break;

                    case 'shipped':
                        if ($order->buyer && $order->buyer->user) {
                            $buyerRoute = $order->buyer->user->hasRole('Buyer') 
                                ? 'buyer.orders' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->buyer->user,
                                '🚚 تم شحن الطلب',
                                "طلبك رقم {$order->order_number} تم شحنه من المورد {$order->supplier->company_name}.",
                                route($buyerRoute, $order->id)
                            );
                        }
                        break;

                    case 'delivered':
                        if ($order->buyer && $order->buyer->user) {
                            $buyerRoute = $order->buyer->user->hasRole('Buyer') 
                                ? 'buyer.orders' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->buyer->user,
                                '✅ تم تسليم الطلب',
                                "تم تأكيد تسليم الطلب رقم {$order->order_number}. شكراً لتعاملك معنا!",
                                route($buyerRoute, $order->id)
                            );
                        }

                        NotificationService::notifyAdmins(
                            '✅ طلب مكتمل',
                            "تم تسليم الطلب رقم {$order->order_number} بنجاح.",
                            route('admin.orders.show', $order->id)
                        );
                        break;

                    case 'cancelled':
                        if ($order->buyer && $order->buyer->user) {
                            $buyerRoute = $order->buyer->user->hasRole('Buyer') 
                                ? 'buyer.orders' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->buyer->user,
                                '❌ تم إلغاء الطلب',
                                "تم إلغاء الطلب رقم {$order->order_number}.",
                                route($buyerRoute, $order->id)
                            );
                        }
                        if ($order->supplier && $order->supplier->user) {
                            $supplierRoute = $order->supplier->user->hasRole('Supplier') 
                                ? 'supplier.orders.show' 
                                : 'admin.orders.show';
                            NotificationService::send(
                                $order->supplier->user,
                                '❌ تم إلغاء الطلب',
                                "تم إلغاء الطلب رقم {$order->order_number}.",
                                route($supplierRoute, $order->id)
                            );
                        }
                        break;
                }
            }

            DB::commit();

            // Dynamic redirect based on user role
            $user = auth()->user();
            $redirectRoute = $user->hasRole('Admin') 
                ? 'admin.orders' 
                : ($user->hasRole('Buyer') 
                    ? 'buyer.orders' 
                    : 'supplier.orders.index');

            return redirect()
                ->route($redirectRoute)
                ->with('success', '✅ تم تحديث أمر الشراء بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث أمر الشراء: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف أمر شراء (Admin Only)
     */
    public function destroy(Order $order): RedirectResponse
    {
        $user = auth()->user();

        // Authorization check - Only admins can delete orders
        if (!$user->hasRole('Admin')) {
            abort(403, 'غير مصرح لك بحذف الطلبات');
        }

        try {
            $orderNumber = $order->order_number;

            $order->delete();

            activity()
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['order_number' => $orderNumber])
                ->log('🗑️ تم حذف أمر الشراء');

            return redirect()
                ->route('admin.orders')
                ->with('success', '❌ تم حذف أمر الشراء بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Order delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف أمر الشراء: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل أمر الشراء
     */
    public function show(Order $order): View
    {
        $user = auth()->user();

        // Authorization check
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            if ($order->buyer_id !== $user->buyerProfile->id) {
                abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
            }
        } elseif ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($order->supplier_id !== $user->supplierProfile->id) {
                abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
            }
        }

        $order->load(['quotation.rfq', 'buyer', 'supplier', 'invoices', 'payments', 'deliveries']);

        // Dynamic view selection
        if ($user->hasRole('Admin')) {
            $view = 'admin.orders.show';
        } elseif ($user->hasRole('Buyer')) {
            $view = 'supplier.orders.show'; // Reuse supplier view for now
        } else {
            $view = 'supplier.orders.show';
        }

        return view($view, compact('order'));
    }

    /**
     * 📥 تصدير الطلبات إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        $filters = request()->only(['search', 'status', 'buyer', 'supplier', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminOrdersExport($filters),
            'orders_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
