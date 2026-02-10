<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use App\Exports\AdminInvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends BaseController
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🧾 عرض قائمة الفواتير
     */
    public function index(): View
    {
        // Permission check is handled by route middleware
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
        $view = $this->getView('admin.invoices.index', 'invoices.index', 'invoices.view');
        
        return view($view, compact('invoices', 'stats'));
    }

    /**
     * ➕ إنشاء فاتورة جديدة
     */
    public function create(): View
    {
        // Filter orders - only show delivered orders that don't have invoices
        $orders = Order::where('status', Order::STATUS_DELIVERED)
            ->whereDoesntHave('invoices', function ($q) {
                $q->where('status', '!=', Invoice::STATUS_CANCELLED);
            })
            ->orderBy('order_number')
            ->pluck('order_number', 'id');

        // If order_id is provided, pre-select it
        $selectedOrderId = request()->get('order_id');
        $selectedOrder = $selectedOrderId ? Order::with('items.product')->find($selectedOrderId) : null;

        // Check if admin or supplier view
        $view = $this->getView('admin.invoices.create', 'invoices.form', 'invoices.create');
        
        return view($view, [
            'invoice' => new Invoice,
            'orders' => $orders,
            'selectedOrderId' => $selectedOrderId,
            'selectedOrder' => $selectedOrder,
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

            // Auto-calculate total_amount if not provided
            if (!isset($data['total_amount']) || $data['total_amount'] === null) {
                $data['total_amount'] = ($data['subtotal'] ?? 0)
                    + ($data['tax'] ?? 0)
                    - ($data['discount'] ?? 0);
                $data['total_amount'] = max(0, $data['total_amount']);
            }

            $invoice = Invoice::create($data);

            // 🔔 إشعارات
            NotificationService::notifyAdmins(
                '🧾 فاتورة جديدة',
                "تم إنشاء فاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                route('admin.invoices.show', $invoice->id)
            );

            // Send notification to buyer
            if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '📄 فاتورة جديدة لطلبك',
                    "تم إصدار فاتورة جديدة للطلب رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            // Send notification to supplier
            if ($invoice->order && $invoice->order->supplier && $invoice->order->supplier->user) {
                NotificationService::send(
                    $invoice->order->supplier->user,
                    '💰 فاتورة جديدة',
                    "تم إنشاء فاتورة متعلقة بطلبك رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
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

            $route = $this->isAdmin('invoices.view') ? 'admin.invoices.index' : 'invoices.index';
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
        $view = $this->getView('admin.invoices.edit', 'invoices.form', 'invoices.update');
        
        return view($view, compact('invoice', 'orders'));
    }

    /**
     * 🔄 تحديث فاتورة
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Auto-calculate total_amount if not explicitly set
            if (!isset($data['total_amount']) || $data['total_amount'] === null) {
                $data['total_amount'] = ($data['subtotal'] ?? $invoice->subtotal)
                    + ($data['tax'] ?? $invoice->tax ?? 0)
                    - ($data['discount'] ?? $invoice->discount ?? 0);
                $data['total_amount'] = max(0, $data['total_amount']);
            }

            // Validate status transition
            if (isset($data['status']) && $data['status'] !== $invoice->status) {
                if (!$invoice->canTransitionTo($data['status'])) {
                    DB::rollBack();
                    return back()->withErrors([
                        'status' => 'لا يمكن تغيير حالة الفاتورة من ' . $invoice->status . ' إلى ' . $data['status']
                    ]);
                }
            }

            // Track approver if status changes to approved
            if (isset($data['status']) && $data['status'] === Invoice::STATUS_APPROVED
                && $invoice->status !== Invoice::STATUS_APPROVED) {
                $data['approved_by'] = Auth::id();
            }

            // Note: updated_by is handled by Auditable trait if present

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
                        route('admin.invoices.show', $invoice->id)
                    );
                }

                NotificationService::notifyAdmins(
                    '💰 فاتورة مدفوعة',
                    "تم سداد الفاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            DB::commit();

            $route = $this->isAdmin('invoices.view') ? 'admin.invoices.index' : 'invoices.index';
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

            $route = $this->isAdmin('invoices.view') ? 'admin.invoices.index' : 'invoices.index';
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
        $view = $this->getView('admin.invoices.show', 'invoices.show', 'invoices.view');
        
        return view($view, compact('invoice'));
    }

    /**
     * ✅ اعتماد فاتورة
     */
    public function approve(Invoice $invoice): RedirectResponse
    {
        $this->authorize('approve', $invoice);

        if ($invoice->status !== Invoice::STATUS_ISSUED) {
            return back()->withErrors(['error' => 'يمكن اعتماد الفواتير الصادرة فقط']);
        }

        DB::beginTransaction();

        try {
            $invoice->update([
                'status' => Invoice::STATUS_APPROVED,
                'approved_by' => Auth::id(),
            ]);

            // Log activity
            activity('invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'old_status' => Invoice::STATUS_ISSUED,
                    'new_status' => Invoice::STATUS_APPROVED,
                ])
                ->log('تم اعتماد الفاتورة');

            // Notify buyer
            if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '✅ تم اعتماد الفاتورة',
                    "تم اعتماد الفاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            // Notify supplier
            if ($invoice->order && $invoice->order->supplier && $invoice->order->supplier->user) {
                NotificationService::send(
                    $invoice->order->supplier->user,
                    '✅ تم اعتماد الفاتورة',
                    "تم اعتماد الفاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            NotificationService::notifyAdmins(
                '✅ تم اعتماد فاتورة',
                "تم اعتماد الفاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                route('admin.invoices.show', $invoice->id)
            );

            DB::commit();

            return back()->with('success', 'تم اعتماد الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice approve error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل اعتماد الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * ❌ إلغاء فاتورة
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return back()->withErrors(['error' => 'الفاتورة ملغاة بالفعل']);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status' => Invoice::STATUS_CANCELLED,
                'notes' => ($invoice->notes ? $invoice->notes . "\n\n" : '')
                    . 'تم الإلغاء: ' . ($validated['cancellation_reason'] ?? 'بدون سبب'),
            ]);

            // Log activity
            activity('invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'old_status' => $oldStatus,
                    'new_status' => Invoice::STATUS_CANCELLED,
                    'cancellation_reason' => $validated['cancellation_reason'] ?? null,
                ])
                ->log('تم إلغاء الفاتورة');

            // Notify buyer
            if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '❌ تم إلغاء الفاتورة',
                    "تم إلغاء الفاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            // Notify supplier
            if ($invoice->order && $invoice->order->supplier && $invoice->order->supplier->user) {
                NotificationService::send(
                    $invoice->order->supplier->user,
                    '❌ تم إلغاء الفاتورة',
                    "تم إلغاء الفاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                    route('admin.invoices.show', $invoice->id)
                );
            }

            NotificationService::notifyAdmins(
                '❌ تم إلغاء فاتورة',
                "تم إلغاء الفاتورة رقم {$invoice->invoice_number} بقيمة {$invoice->total_amount}.",
                route('admin.invoices.show', $invoice->id)
            );

            DB::commit();

            return back()->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice cancel error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل إلغاء الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * 📥 تصدير الفواتير إلى Excel (Admin Only)
     */
    public function export(): BinaryFileResponse
    {
        if (!$this->isAdmin('invoices.export')) {
            abort(403);
        }

        $filters = request()->only(['search', 'status', 'payment_status', 'from_date', 'to_date']);
        
        return Excel::download(
            new AdminInvoicesExport($filters),
            'invoices_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
