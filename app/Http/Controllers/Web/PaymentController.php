<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use App\Exports\AdminPaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 💰 عرض جميع عمليات الدفع
     */
    public function index(): View
    {
        $query = Payment::with(['invoice', 'order', 'buyer', 'supplier']);

        // Apply filters
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('buyer', fn($sub) => $sub->where('organization_name', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($sub) => $sub->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('from_date')) {
            $query->whereDate('paid_at', '>=', request('from_date'));
        }

        if (request()->filled('to_date')) {
            $query->whereDate('paid_at', '<=', request('to_date'));
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        // Calculate stats
        $stats = Payment::selectRaw('
            COUNT(*) as total,
            COALESCE(SUM(amount), 0) as total_amount,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
        ', [
            Payment::STATUS_COMPLETED,
            Payment::STATUS_PENDING,
            Payment::STATUS_FAILED,
        ])->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'completed' => $stats->completed ?? 0,
            'pending' => $stats->pending ?? 0,
            'failed' => $stats->failed ?? 0,
        ];

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.payments.index' : 'payments.index';
        
        return view($view, compact('payments', 'stats'));
    }

    /**
     * ➕ إنشاء دفعة جديدة
     */
    public function create(): View
    {
        $invoices = Invoice::pluck('invoice_number', 'id');
        $orders = Order::pluck('order_number', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.payments.create' : 'payments.form';
        
        return view($view, [
            'payment' => new Payment,
            'invoices' => $invoices,
            'orders' => $orders,
            'buyers' => $buyers,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * 💾 تخزين عملية دفع جديدة
     */
    public function store(PaymentRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['payment_reference'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_PAYMENT,
                \App\Models\Payment::class,
                'payment_reference'
            );
            $data['processed_by'] = Auth::id();

            $payment = Payment::create($data);

            // ✅ تحديث حالة الفاتورة تلقائيًا
            if ($payment->invoice) {
                $totalPaid = $payment->invoice->payments()->sum('amount');
                $invoiceTotal = $payment->invoice->total_amount;

                if ($totalPaid >= $invoiceTotal) {
                    $payment->invoice->update(['payment_status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $payment->invoice->update(['payment_status' => 'partial']);
                } else {
                    $payment->invoice->update(['payment_status' => 'unpaid']);
                }
            }

            // 🔔 إشعارات لجميع الأطراف
            NotificationService::notifyAdmins(
                '💰 دفعة مالية جديدة',
                "تم تسجيل دفعة رقم {$payment->payment_reference} بقيمة {$payment->amount} {$payment->currency}.",
                route('payments.show', $payment->id)
            );

            if ($payment->supplier?->user) {
                NotificationService::send(
                    $payment->supplier->user,
                    '💵 تم استلام دفعة جديدة',
                    "تم تسجيل دفعة جديدة تخص الطلب رقم {$payment->order?->order_number}.",
                    route('payments.show', $payment->id)
                );
            }

            if ($payment->buyer?->user) {
                NotificationService::send(
                    $payment->buyer->user,
                    '✅ تم تسجيل دفعتك بنجاح',
                    "تم تسجيل دفعتك بمبلغ {$payment->amount} {$payment->currency}.",
                    route('payments.show', $payment->id)
                );
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'processed_by' => Auth::id(),
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'invoice_id' => $payment->invoice_id,
                    'order_id' => $payment->order_id,
                    'ip' => request()->ip(),
                ])
                ->log('💵 تم تسجيل دفعة مالية جديدة');

            DB::commit();

            $route = auth()->user()->hasRole('Admin') ? 'admin.payments.index' : 'payments.index';
            return redirect()
                ->route($route)
                ->with('success', '✅ تم تسجيل الدفعة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Payment store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة الدفعة: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل عملية دفع
     */
    public function edit(Payment $payment): View
    {
        $invoices = Invoice::pluck('invoice_number', 'id');
        $orders = Order::pluck('order_number', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.payments.edit' : 'payments.form';
        
        return view($view, compact('payment', 'invoices', 'orders', 'buyers', 'suppliers'));
    }

    /**
     * 🔄 تحديث بيانات الدفع
     */
    public function update(PaymentRequest $request, Payment $payment): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $payment->update($request->validated());

            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->withProperties(['updated_by' => Auth::id()])
                ->log('💵 تم تعديل الدفعة المالية');

            DB::commit();

            $route = auth()->user()->hasRole('Admin') ? 'admin.payments.index' : 'payments.index';
            return redirect()
                ->route($route)
                ->with('success', '✅ تم تحديث بيانات الدفعة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Payment update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث الدفعة: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف عملية دفع
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        try {
            $payment->delete();

            activity()
                ->performedOn($payment)
                ->log('🗑️ تم حذف الدفعة المالية');

            $route = auth()->user()->hasRole('Admin') ? 'admin.payments.index' : 'payments.index';
            return redirect()
                ->route($route)
                ->with('success', '❌ تم حذف الدفعة بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Payment delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف الدفعة: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل الدفع
     */
    public function show(Payment $payment): View
    {
        $payment->load(['invoice', 'order', 'buyer', 'supplier']);

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.payments.show' : 'payments.show';
        
        return view($view, compact('payment'));
    }

    /**
     * 📥 تصدير المدفوعات إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $filters = request()->only(['search', 'status', 'method', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminPaymentsExport($filters),
            'payments_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
