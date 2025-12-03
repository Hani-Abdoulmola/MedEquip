<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Buyer;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📦 قائمة أوامر الشراء (Admin View Only)
     */
    public function index()
    {
        $query = Order::with(['quotation.rfq', 'buyer', 'supplier', 'items']);

        // Filters
        if (request()->filled('buyer')) {
            $query->where('buyer_id', request('buyer'));
        }

        if (request()->filled('supplier')) {
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

        // Stats
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
        ];

        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('admin.orders.index', compact('orders', 'stats', 'buyers', 'suppliers'));
    }

    /**
     * ➕ إنشاء أمر شراء جديد (Buyer Role)
     */
    public function create()
    {
        $quotations = \App\Models\Quotation::where('status', 'accepted')->pluck('reference_code', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('orders.form', [
            'order' => new Order,
            'quotations' => $quotations,
            'buyers' => $buyers,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * 💾 تخزين أمر شراء جديد (Buyer Role)
     */
    public function store(OrderRequest $request)
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
                NotificationService::send(
                    $order->buyer->user,
                    '🛒 تم إنشاء طلبك بنجاح',
                    "تم إنشاء الطلب رقم {$order->order_number}. يمكنك متابعة حالته من لوحة التحكم.",
                    route('admin.orders.show', $order->id)
                );
            }

            // Send notification to supplier
            if ($order->supplier && $order->supplier->user) {
                NotificationService::send(
                    $order->supplier->user,
                    '📦 طلب جديد من مشتري',
                    "تم إرسال طلب جديد من {$order->buyer->organization_name}.",
                    route('admin.orders.show', $order->id)
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

            return redirect()
                ->route('admin.orders')
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
    public function edit(Order $order)
    {
        $order->load(['quotation.rfq', 'buyer', 'supplier', 'items']);

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * 🔄 تحديث بيانات أمر الشراء (Admin Only - Status & Notes)
     */
    public function update(OrderRequest $request, Order $order)
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
                            NotificationService::send(
                                $order->buyer->user,
                                '🔄 جاري تجهيز طلبك',
                                "طلبك رقم {$order->order_number} الآن قيد التجهيز.",
                                route('admin.orders.show', $order->id)
                            );
                        }
                        if ($order->supplier && $order->supplier->user) {
                            NotificationService::send(
                                $order->supplier->user,
                                '🔄 طلب قيد التجهيز',
                                "الطلب رقم {$order->order_number} الآن قيد التجهيز.",
                                route('admin.orders.show', $order->id)
                            );
                        }
                        break;

                    case 'shipped':
                        if ($order->buyer && $order->buyer->user) {
                            NotificationService::send(
                                $order->buyer->user,
                                '🚚 تم شحن الطلب',
                                "طلبك رقم {$order->order_number} تم شحنه من المورد {$order->supplier->company_name}.",
                                route('admin.orders.show', $order->id)
                            );
                        }
                        break;

                    case 'delivered':
                        if ($order->buyer && $order->buyer->user) {
                            NotificationService::send(
                                $order->buyer->user,
                                '✅ تم تسليم الطلب',
                                "تم تأكيد تسليم الطلب رقم {$order->order_number}. شكراً لتعاملك معنا!",
                                route('admin.orders.show', $order->id)
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
                            NotificationService::send(
                                $order->buyer->user,
                                '❌ تم إلغاء الطلب',
                                "تم إلغاء الطلب رقم {$order->order_number}.",
                                route('admin.orders.show', $order->id)
                            );
                        }
                        if ($order->supplier && $order->supplier->user) {
                            NotificationService::send(
                                $order->supplier->user,
                                '❌ تم إلغاء الطلب',
                                "تم إلغاء الطلب رقم {$order->order_number}.",
                                route('admin.orders.show', $order->id)
                            );
                        }
                        break;
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.orders')
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
    public function destroy(Order $order)
    {
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
    public function show(Order $order)
    {
        $order->load(['quotation.rfq', 'buyer', 'supplier', 'invoices', 'payments', 'deliveries']);

        return view('admin.orders.show', compact('order'));
    }
}
