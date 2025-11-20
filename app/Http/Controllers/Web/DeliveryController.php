<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryRequest;
use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🚚 عرض كل عمليات التسليم
     */
    public function index()
    {
        $deliveries = Delivery::with(['order', 'supplier', 'buyer'])
            ->latest('id')
            ->paginate(20);

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * ➕ إنشاء عملية تسليم جديدة
     */
    public function create()
    {
        $orders = Order::pluck('order_number', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');

        return view('deliveries.form', [
            'delivery' => new Delivery,
            'orders' => $orders,
            'suppliers' => $suppliers,
            'buyers' => $buyers,
        ]);
    }

    /**
     * 💾 تخزين عملية تسليم جديدة
     */
    public function store(DeliveryRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['delivery_number'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_DELIVERY,
                \App\Models\Delivery::class,
                'delivery_number'
            );
            $data['created_by'] = auth()->id();

            // ✅ تحقق من أن الطلب غير مرتبط بتسليم سابق
            if (Delivery::where('order_id', $data['order_id'])->exists()) {
                return back()
                    ->withErrors(['order_id' => '⚠️ هذا الطلب مرتبط بعملية تسليم موجودة بالفعل.'])
                    ->withInput();
            }

            // 💾 إنشاء التسليم
            $delivery = Delivery::create($data);

            // 🔔 إشعارات عند الإنشاء
            NotificationService::notifyAdmins(
                'تسليم جديد',
                "تم تسجيل عملية تسليم رقم {$delivery->delivery_number} من المورد {$delivery->supplier->company_name}.",
                route('deliveries.show', $delivery->id)
            );

            NotificationService::send(
                $delivery->buyer->user,
                'عملية تسليم جديدة',
                'تم إنشاء تسليم جديد لطلبك.',
                route('deliveries.show', $delivery->id)
            );

            NotificationService::send(
                $delivery->supplier->user,
                'تأكيد التسليم',
                'يرجى مراجعة تفاصيل التسليم.',
                route('deliveries.show', $delivery->id)
            );

            // 🧾 تسجيل النشاط
            activity()
                ->performedOn($delivery)
                ->withProperties(['created_by' => auth()->id()])
                ->log('🚚 تم تسجيل عملية تسليم جديدة');

            DB::commit();

            return redirect()
                ->route('deliveries.index')
                ->with('success', '✅ تم تسجيل عملية التسليم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Delivery store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل في تسجيل عملية التسليم: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل عملية تسليم
     */
    public function edit(Delivery $delivery)
    {
        $orders = Order::pluck('order_number', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');

        return view('deliveries.form', compact('delivery', 'orders', 'suppliers', 'buyers'));
    }

    /**
     * 🔄 تحديث عملية تسليم (بما في ذلك إشعار عند التأكيد)
     */
    public function update(DeliveryRequest $request, Delivery $delivery)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            $delivery->update($data);

            activity()
                ->performedOn($delivery)
                ->withProperties(['updated_by' => auth()->id()])
                ->log('🚚 تم تحديث عملية التسليم');

            // 🔔 إشعارات ذكية عند تغيير الحالة إلى "delivered"
            if ($delivery->status === 'delivered') {
                NotificationService::send(
                    $delivery->buyer->user,
                    '📦 تم تسليم طلبك',
                    "تم تأكيد تسليم طلبك رقم {$delivery->order->order_number} بنجاح.",
                    route('deliveries.show', $delivery->id)
                );

                NotificationService::send(
                    $delivery->supplier->user,
                    '✅ تم تأكيد عملية التسليم',
                    "تم تأكيد تسليم الطلب رقم {$delivery->order->order_number} إلى المشتري.",
                    route('deliveries.show', $delivery->id)
                );

                NotificationService::notifyAdmins(
                    'تأكيد تسليم مكتمل',
                    "تم إكمال عملية تسليم رقم {$delivery->delivery_number} بنجاح.",
                    route('deliveries.show', $delivery->id)
                );
            }

            DB::commit();

            return redirect()
                ->route('deliveries.index')
                ->with('success', '✅ تم تحديث بيانات التسليم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Delivery update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث عملية التسليم: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف عملية تسليم
     */
    public function destroy(Delivery $delivery)
    {
        try {
            $delivery->delete();

            activity()
                ->performedOn($delivery)
                ->log('🗑️ تم حذف عملية التسليم');

            return redirect()
                ->route('deliveries.index')
                ->with('success', '❌ تم حذف عملية التسليم بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Delivery delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف عملية التسليم: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل عملية التسليم
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['order', 'supplier', 'buyer', 'creator', 'verifier', 'files']);

        return view('deliveries.show', compact('delivery'));
    }
}
