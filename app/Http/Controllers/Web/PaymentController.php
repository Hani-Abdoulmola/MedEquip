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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 💰 عرض جميع عمليات الدفع
     */
    public function index()
    {
        $payments = Payment::with(['invoice', 'order', 'buyer', 'supplier'])
            ->latest('id')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * ➕ إنشاء دفعة جديدة
     */
    public function create()
    {
        $invoices = Invoice::pluck('invoice_number', 'id');
        $orders = Order::pluck('order_number', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('payments.form', [
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
    public function store(PaymentRequest $request)
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

            return redirect()
                ->route('payments.index')
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
    public function edit(Payment $payment)
    {
        $invoices = Invoice::pluck('invoice_number', 'id');
        $orders = Order::pluck('order_number', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('payments.form', compact('payment', 'invoices', 'orders', 'buyers', 'suppliers'));
    }

    /**
     * 🔄 تحديث بيانات الدفع
     */
    public function update(PaymentRequest $request, Payment $payment)
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

            return redirect()
                ->route('payments.index')
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
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();

            activity()
                ->performedOn($payment)
                ->log('🗑️ تم حذف الدفعة المالية');

            return redirect()
                ->route('payments.index')
                ->with('success', '❌ تم حذف الدفعة بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Payment delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف الدفعة: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل الدفع
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice', 'order', 'buyer', 'supplier']);

        return view('payments.show', compact('payment'));
    }
}
