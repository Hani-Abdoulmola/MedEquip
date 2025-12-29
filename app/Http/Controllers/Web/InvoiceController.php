<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use App\Exports\AdminInvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🧾 عرض قائمة الفواتير
     */
    public function index(): View
    {
        $query = Invoice::with(['order.buyer', 'order.supplier']);

        // Apply filters
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('buyer', fn($buyer) => $buyer->where('organization_name', 'like', "%{$search}%"))
                          ->orWhereHas('supplier', fn($supplier) => $supplier->where('company_name', 'like', "%{$search}%"));
                  });
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        if (request()->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', request('from_date'));
        }

        if (request()->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', request('to_date'));
        }

        $invoices = $query->latest('invoice_date')->paginate(20)->withQueryString();

        // Calculate stats
        $stats = Invoice::selectRaw('
            COUNT(*) as total,
            COALESCE(SUM(total_amount), 0) as total_amount,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as paid,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as unpaid,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as partial,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as issued,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled
        ', [
            Invoice::PAYMENT_PAID,
            Invoice::PAYMENT_UNPAID,
            Invoice::PAYMENT_PARTIAL,
            Invoice::STATUS_ISSUED,
            Invoice::STATUS_APPROVED,
            Invoice::STATUS_CANCELLED,
        ])->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'paid' => $stats->paid ?? 0,
            'unpaid' => $stats->unpaid ?? 0,
            'partial' => $stats->partial ?? 0,
            'issued' => $stats->issued ?? 0,
            'approved' => $stats->approved ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
        ];

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.invoices.index' : 'invoices.index';
        
        return view($view, compact('invoices', 'stats'));
    }

    /**
     * ➕ إنشاء فاتورة جديدة
     */
    public function create(): View
    {
        $orders = Order::orderBy('order_number')->pluck('order_number', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.invoices.create' : 'invoices.form';
        
        return view($view, [
            'invoice' => new Invoice,
            'orders' => $orders,
        ]);
    }

    /**
     * 💾 تخزين فاتورة جديدة
     */
    public function store(InvoiceRequest $request): RedirectResponse
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

            $route = auth()->user()->hasRole('Admin') ? 'admin.invoices.index' : 'invoices.index';
            return redirect()
                ->route($route)
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
    public function edit(Invoice $invoice): View
    {
        $orders = Order::orderBy('order_number')->pluck('order_number', 'id');

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.invoices.edit' : 'invoices.form';
        
        return view($view, compact('invoice', 'orders'));
    }

    /**
     * 🔄 تحديث فاتورة
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
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

            $route = auth()->user()->hasRole('Admin') ? 'admin.invoices.index' : 'invoices.index';
            return redirect()
                ->route($route)
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
    public function destroy(Invoice $invoice): RedirectResponse
    {
        try {
            $invoice->delete();

            activity()
                ->performedOn($invoice)
                ->log('🗑️ تم حذف الفاتورة');

            $route = auth()->user()->hasRole('Admin') ? 'admin.invoices.index' : 'invoices.index';
            return redirect()
                ->route($route)
                ->with('success', '❌ تم حذف الفاتورة بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Invoice delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف الفاتورة: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل الفاتورة
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load(['order.buyer', 'order.supplier', 'payments']);

        // Check if admin or supplier view
        $view = auth()->user()->hasRole('Admin') ? 'admin.invoices.show' : 'invoices.show';
        
        return view($view, compact('invoice'));
    }

    /**
     * 📥 تصدير الفواتير إلى Excel (Admin Only)
     */
    public function export(): BinaryFileResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $filters = request()->only(['search', 'status', 'payment_status', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminInvoicesExport($filters),
            'invoices_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
