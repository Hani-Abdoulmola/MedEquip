<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🧾 عرض قائمة الفواتير
     */
    public function index()
    {
        $invoices = Invoice::with(['order.buyer', 'order.supplier'])
            ->latest('id')
            ->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * ➕ إنشاء فاتورة جديدة
     */
    public function create()
    {
        $orders = Order::orderBy('order_number')->pluck('order_number', 'id');

        return view('invoices.form', [
            'invoice' => new Invoice,
            'orders' => $orders,
        ]);
    }

    /**
     * 💾 تخزين فاتورة جديدة
     */
    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['invoice_number'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_INVOICE,
                \App\Models\Invoice::class,
                'invoice_number'
            );
            $data['created_by'] = Auth::id();

            $invoice = Invoice::create($data);

            // 🔔 إشعارات
            NotificationService::notifyAdmins(
                '🧾 فاتورة جديدة',
                "تم إنشاء فاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                route('invoices.show', $invoice->id)
            );

            // Send notification to buyer
            if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '📄 فاتورة جديدة لطلبك',
                    "تم إصدار فاتورة جديدة للطلب رقم {$invoice->order->order_number}.",
                    route('invoices.show', $invoice->id)
                );
            }

            // Send notification to supplier
            if ($invoice->order && $invoice->order->supplier && $invoice->order->supplier->user) {
                NotificationService::send(
                    $invoice->order->supplier->user,
                    '💰 فاتورة جديدة',
                    "تم إنشاء فاتورة متعلقة بطلبك رقم {$invoice->order->order_number}.",
                    route('invoices.show', $invoice->id)
                );
            }

            activity()
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'created_by' => Auth::id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('🧾 تم إنشاء فاتورة جديدة');

            DB::commit();

            return redirect()
                ->route('invoices.index')
                ->with('success', '✅ تم إنشاء الفاتورة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل إنشاء الفاتورة: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل فاتورة
     */
    public function edit(Invoice $invoice)
    {
        $orders = Order::orderBy('order_number')->pluck('order_number', 'id');

        return view('invoices.form', compact('invoice', 'orders'));
    }

    /**
     * 🔄 تحديث فاتورة
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $invoice->update($data);

            // 🧾 سجل النشاط
            activity()
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties(['updated_by' => Auth::id()])
                ->log('🧾 تم تحديث الفاتورة');

            // 🔔 إشعار عند الدفع الكامل
            if ($invoice->payment_status === 'paid') {
                // Send notification to buyer
                if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
                    NotificationService::send(
                        $invoice->order->buyer->user,
                        '✅ تم تأكيد الدفع',
                        "تم تأكيد دفع الفاتورة رقم {$invoice->invoice_number}. شكراً لتعاملكم.",
                        route('invoices.show', $invoice->id)
                    );
                }

                NotificationService::notifyAdmins(
                    '💰 فاتورة مدفوعة',
                    "تم سداد الفاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                    route('invoices.show', $invoice->id)
                );
            }

            DB::commit();

            return redirect()
                ->route('invoices.index')
                ->with('success', '✅ تم تحديث الفاتورة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث الفاتورة: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف فاتورة
     */
    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete();

            activity()
                ->performedOn($invoice)
                ->log('🗑️ تم حذف الفاتورة');

            return redirect()
                ->route('invoices.index')
                ->with('success', '❌ تم حذف الفاتورة بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Invoice delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف الفاتورة: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل الفاتورة
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.buyer', 'order.supplier', 'payments']);

        return view('invoices.show', compact('invoice'));
    }
}
