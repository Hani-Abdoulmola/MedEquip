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
use App\Exports\AdminDeliveriesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeliveryController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🚚 عرض كل عمليات التسليم
     */
    public function index(): View
    {
        // Check permission
        if (!auth()->user()->can('deliveries.view')) {
            abort(403, 'ليس لديك صلاحية عرض عمليات التسليم');
        }
        
        $query = Delivery::with(['order', 'supplier', 'buyer']);

        // Apply filters
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('buyer', fn($sub) => $sub->where('organization_name', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($sub) => $sub->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('from_date')) {
            $query->whereDate('delivery_date', '>=', request('from_date'));
        }

        if (request()->filled('to_date')) {
            $query->whereDate('delivery_date', '<=', request('to_date'));
        }

        $deliveries = $query->latest('delivery_date')->paginate(20)->withQueryString();

        // Calculate stats
        $stats = Delivery::selectRaw('
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
        ])->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'in_transit' => $stats->in_transit ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'failed' => $stats->failed ?? 0,
        ];

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.deliveries.index' : 'deliveries.index';
        
        return view($view, compact('deliveries', 'stats'));
    }

    /**
     * ➕ إنشاء عملية تسليم جديدة
     */
    public function create(): View
    {
        $orders = Order::pluck('order_number', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.deliveries.create' : 'deliveries.form';
        
        return view($view, [
            'delivery' => new Delivery,
            'orders' => $orders,
            'suppliers' => $suppliers,
            'buyers' => $buyers,
        ]);
    }

    /**
     * 💾 تخزين عملية تسليم جديدة
     */
    public function store(DeliveryRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['delivery_number'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_DELIVERY,
                \App\Models\Delivery::class,
                'delivery_number'
            );
            /** @var \App\Models\User */
            $authUser = Auth::user();
            $data['created_by'] = $authUser->id;

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

            // Send notification to buyer
            if ($delivery->buyer && $delivery->buyer->user) {
                NotificationService::send(
                    $delivery->buyer->user,
                    'عملية تسليم جديدة',
                    'تم إنشاء تسليم جديد لطلبك.',
                    route('deliveries.show', $delivery->id)
                );
            }

            // Send notification to supplier
            if ($delivery->supplier && $delivery->supplier->user) {
                NotificationService::send(
                    $delivery->supplier->user,
                    'تأكيد التسليم',
                    'يرجى مراجعة تفاصيل التسليم.',
                    route('deliveries.show', $delivery->id)
                );
            }

            // 🧾 تسجيل النشاط
            activity()
                ->performedOn($delivery)
                ->withProperties(['created_by' => $authUser->id])
                ->log('🚚 تم تسجيل عملية تسليم جديدة');

            DB::commit();

            $route = auth()->user()->hasRole('Admin') ? 'admin.deliveries.index' : 'deliveries.index';
            return redirect()
                ->route($route)
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
    public function edit(Delivery $delivery): View
    {
        $orders = Order::pluck('order_number', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.deliveries.edit' : 'deliveries.form';
        
        return view($view, compact('delivery', 'orders', 'suppliers', 'buyers'));
    }

    /**
     * 🔄 تحديث عملية تسليم (بما في ذلك إشعار عند التأكيد)
     */
    public function update(DeliveryRequest $request, Delivery $delivery): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            /** @var \App\Models\User */
            $authUser = Auth::user();
            $data['updated_by'] = $authUser->id;

            $delivery->update($data);

            activity()
                ->performedOn($delivery)
                ->withProperties(['updated_by' => $authUser->id])
                ->log('🚚 تم تحديث عملية التسليم');

            // 🔔 إشعارات ذكية عند تغيير الحالة إلى "delivered"
            if ($delivery->status === 'delivered') {
                // Send notification to buyer
                if ($delivery->buyer && $delivery->buyer->user) {
                    NotificationService::send(
                        $delivery->buyer->user,
                        '📦 تم تسليم طلبك',
                        "تم تأكيد تسليم طلبك رقم {$delivery->order->order_number} بنجاح.",
                        route('deliveries.show', $delivery->id)
                    );
                }

                // Send notification to supplier
                if ($delivery->supplier && $delivery->supplier->user) {
                    NotificationService::send(
                        $delivery->supplier->user,
                        '✅ تم تأكيد عملية التسليم',
                        "تم تأكيد تسليم الطلب رقم {$delivery->order->order_number} إلى المشتري.",
                        route('deliveries.show', $delivery->id)
                    );
                }

                NotificationService::notifyAdmins(
                    'تأكيد تسليم مكتمل',
                    "تم إكمال عملية تسليم رقم {$delivery->delivery_number} بنجاح.",
                    route('deliveries.show', $delivery->id)
                );
            }

            DB::commit();

            $route = auth()->user()->hasRole('Admin') ? 'admin.deliveries.index' : 'deliveries.index';
            return redirect()
                ->route($route)
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
    public function destroy(Delivery $delivery): RedirectResponse
    {
        try {
            $delivery->delete();

            activity()
                ->performedOn($delivery)
                ->log('🗑️ تم حذف عملية التسليم');

            $route = auth()->user()->hasRole('Admin') ? 'admin.deliveries.index' : 'deliveries.index';
            return redirect()
                ->route($route)
                ->with('success', '❌ تم حذف عملية التسليم بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Delivery delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف عملية التسليم: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل عملية التسليم
     */
    public function show(Delivery $delivery): View
    {
        $delivery->load(['order', 'supplier', 'buyer', 'creator', 'verifier']);

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.deliveries.show' : 'deliveries.show';
        
        return view($view, compact('delivery'));
    }

    /**
     * 📥 تصدير التسليمات إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $filters = request()->only(['search', 'status', 'supplier', 'buyer', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminDeliveriesExport($filters),
            'deliveries_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
