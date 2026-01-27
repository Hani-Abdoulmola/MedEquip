<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Exports\SupplierInvoicesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Supplier Invoice Controller
 *
 * Handles invoice viewing for suppliers.
 * Suppliers can view invoices related to their orders.
 */
class SupplierInvoiceController extends Controller
{
    /**
     * Display list of invoices for the supplier.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Invoice::with(['order.buyer'])
            ->whereHas('order', function ($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            });

        // Filter by status (supports multiple statuses)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by payment status (supports multiple statuses)
        if ($request->filled('payment_status')) {
            $paymentStatuses = is_array($request->payment_status) ? $request->payment_status : [$request->payment_status];
            $query->whereIn('payment_status', $paymentStatuses);
        }

        // Date range filter with quick filters
        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            match ($dateFilter) {
                'today' => $query->whereDate('invoice_date', today()),
                'this_week' => $query->whereBetween('invoice_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year),
                'last_month' => $query->whereMonth('invoice_date', now()->subMonth()->month)->whereYear('invoice_date', now()->subMonth()->year),
                default => null,
            };
        } else {
            // Custom date range
            if ($request->filled('from_date')) {
                $query->whereDate('invoice_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('invoice_date', '<=', $request->to_date);
            }
        }

        // Enhanced search across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('buyer', fn($buyer) => $buyer->where('organization_name', 'like', "%{$search}%"));
                  });
            });
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        // Optimized stats calculation using single query
        $stats = Invoice::whereHas('order', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })
        ->selectRaw('
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
        ])
        ->first();

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

        // Log activity
        activity('supplier_invoices')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => $request->only(['status', 'payment_status', 'from_date', 'to_date', 'search']),
            ])
            ->log('عرض المورد قائمة الفواتير');

        return view('supplier.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Display invoice details.
     */
    public function show(Invoice $invoice): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Check if invoice belongs to supplier's order
        if (!$invoice->order || $invoice->order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الفاتورة');
        }

        $invoice->load(['order.items.product', 'order.buyer', 'payments', 'creator', 'approver']);

        // Log activity
        activity('supplier_invoices')
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'invoice_number' => $invoice->invoice_number,
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'total_amount' => $invoice->total_amount,
                'payment_status' => $invoice->payment_status,
                'status' => $invoice->status,
            ])
            ->log('عرض المورد تفاصيل الفاتورة: ' . $invoice->invoice_number);

        return view('supplier.invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice): Response
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Check if invoice belongs to supplier's order
        if (!$invoice->order || $invoice->order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتحميل هذه الفاتورة');
        }

        $invoice->load(['order.items.product', 'order.buyer', 'payments', 'creator', 'approver']);

        // Log activity
        activity('supplier_invoices')
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'invoice_number' => $invoice->invoice_number,
                'action' => 'download',
            ])
            ->log('قام المورد بتحميل الفاتورة: ' . $invoice->invoice_number);

        // Generate PDF view
        $pdf = PDF::loadView('supplier.invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Export invoices to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $filters = $request->only(['status', 'payment_status', 'from_date', 'to_date']);

        // Log activity
        activity('supplier_invoices')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'action' => 'export',
                'filters' => $filters,
            ])
            ->log('قام المورد بتصدير قائمة الفواتير');

        $fileName = 'invoices-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new SupplierInvoicesExport($supplier->id, $filters), $fileName);
    }

    /**
     * Show form to create a new invoice.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Get delivered orders that don't have invoices yet
        $orders = Order::where('supplier_id', $supplier->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->whereDoesntHave('invoices', function ($q) {
                $q->where('status', '!=', Invoice::STATUS_CANCELLED);
            })
            ->orderBy('order_number')
            ->pluck('order_number', 'id');

        // If order_id is provided, pre-select it
        $selectedOrderId = $request->get('order_id');

        return view('supplier.invoices.create', compact('orders', 'selectedOrderId'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(InvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Verify order belongs to supplier
            $order = Order::findOrFail($data['order_id']);
            if ($order->supplier_id !== $supplier->id) {
                return back()->withErrors(['error' => 'ليس لديك صلاحية لإنشاء فاتورة لهذا الطلب']);
            }

            // Check if invoice already exists for this order
            $existingInvoice = Invoice::where('order_id', $order->id)
                ->where('status', '!=', Invoice::STATUS_CANCELLED)
                ->first();

            if ($existingInvoice) {
                return back()->withErrors(['error' => 'يوجد فاتورة موجودة بالفعل لهذا الطلب']);
            }

            // Auto-calculate total_amount if not provided
            if (!isset($data['total_amount']) || $data['total_amount'] === null) {
                $data['total_amount'] = ($data['subtotal'] ?? 0)
                    + ($data['tax'] ?? 0)
                    - ($data['discount'] ?? 0);
                $data['total_amount'] = max(0, $data['total_amount']);
            }

            $data['invoice_number'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_INVOICE,
                Invoice::class,
                'invoice_number'
            );
            $data['created_by'] = Auth::id();
            $data['status'] = $data['status'] ?? Invoice::STATUS_DRAFT;

            $invoice = Invoice::create($data);

            // Handle file uploads if any
            if ($request->hasFile('invoice_documents')) {
                foreach ($request->file('invoice_documents') as $file) {
                    $invoice->addMediaFromRequest('invoice_documents[]')
                        ->toMediaCollection('invoice_documents');
                }
            }

            // Log activity
            activity('supplier_invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'order_id' => $order->id,
                    'total_amount' => $invoice->total_amount,
                ])
                ->log('قام المورد بإنشاء فاتورة جديدة');

            // Notify buyer
            if ($order->buyer && $order->buyer->user) {
                NotificationService::send(
                    $order->buyer->user,
                    '📄 فاتورة جديدة لطلبك',
                    "تم إصدار فاتورة جديدة للطلب رقم {$order->order_number}.",
                    route('buyer.invoices.show', $invoice->id)
                );
            }

            // Notify admins
            NotificationService::notifyAdmins(
                '🧾 فاتورة جديدة من مورد',
                "قام المورد {$supplier->company_name} بإنشاء فاتورة رقم {$invoice->invoice_number} للطلب رقم {$order->order_number}.",
                route('admin.invoices.show', $invoice->id)
            );

            DB::commit();

            return redirect()
                ->route('supplier.invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier invoice creation error', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل إنشاء الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to edit an invoice.
     */
    public function edit(Invoice $invoice): View|RedirectResponse
    {
        $this->authorize('update', $invoice);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify invoice belongs to supplier
        if (!$invoice->order || $invoice->order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه الفاتورة');
        }

        // Check if invoice can be edited
        if (!in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_ISSUED])) {
            return redirect()
                ->route('supplier.invoices.show', $invoice)
                ->with('error', 'لا يمكن تعديل الفاتورة بعد اعتمادها');
        }

        $invoice->load(['order.items.product', 'order.buyer']);

        return view('supplier.invoices.edit', compact('invoice'));
    }

    /**
     * Update an invoice.
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify invoice belongs to supplier
        if (!$invoice->order || $invoice->order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه الفاتورة');
        }

        // Check if invoice can be edited
        if (!in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_ISSUED])) {
            return back()->withErrors(['error' => 'لا يمكن تعديل الفاتورة بعد اعتمادها']);
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Auto-calculate total_amount if not provided
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

            $oldStatus = $invoice->status;
            $invoice->update($data);

            // Handle file uploads if any
            if ($request->hasFile('invoice_documents')) {
                foreach ($request->file('invoice_documents') as $file) {
                    $invoice->addMediaFromRequest('invoice_documents[]')
                        ->toMediaCollection('invoice_documents');
                }
            }

            // Log activity
            activity('supplier_invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'old_status' => $oldStatus,
                    'new_status' => $invoice->status,
                ])
                ->log('قام المورد بتحديث الفاتورة');

            // If status changed to issued, notify buyer
            if ($oldStatus !== Invoice::STATUS_ISSUED && $invoice->status === Invoice::STATUS_ISSUED) {
                if ($invoice->order->buyer && $invoice->order->buyer->user) {
                    NotificationService::send(
                        $invoice->order->buyer->user,
                        '📄 فاتورة جديدة لطلبك',
                        "تم إصدار فاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                        route('buyer.invoices.show', $invoice->id)
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('supplier.invoices.show', $invoice)
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier invoice update error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل تحديث الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel an invoice.
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('cancel', $invoice);

        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

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
            activity('supplier_invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'old_status' => $oldStatus,
                    'new_status' => Invoice::STATUS_CANCELLED,
                    'cancellation_reason' => $validated['cancellation_reason'] ?? null,
                ])
                ->log('قام المورد بإلغاء الفاتورة');

            // Notify buyer
            if ($invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '❌ تم إلغاء الفاتورة',
                    "تم إلغاء الفاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number}.",
                    route('buyer.invoices.show', $invoice->id)
                );
            }

            // Notify admins
            NotificationService::notifyAdmins(
                '❌ تم إلغاء فاتورة',
                "قام المورد {$supplier->company_name} بإلغاء الفاتورة رقم {$invoice->invoice_number}.",
                route('admin.invoices.show', $invoice->id)
            );

            DB::commit();

            return back()->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier invoice cancel error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل إلغاء الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * Send invoice to buyer via notification.
     */
    public function sendToBuyer(Invoice $invoice): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Verify invoice belongs to supplier
        if (!$invoice->order || $invoice->order->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لإرسال هذه الفاتورة');
        }

        try {
            // Update status to issued if still draft
            if ($invoice->status === Invoice::STATUS_DRAFT) {
                $invoice->update(['status' => Invoice::STATUS_ISSUED]);
            }

            // Notify buyer
            if ($invoice->order->buyer && $invoice->order->buyer->user) {
                NotificationService::send(
                    $invoice->order->buyer->user,
                    '📄 فاتورة جديدة لطلبك',
                    "تم إصدار فاتورة رقم {$invoice->invoice_number} للطلب رقم {$invoice->order->order_number} بقيمة {$invoice->total_amount} د.ل. يرجى مراجعة الفاتورة.",
                    route('buyer.invoices.show', $invoice->id)
                );
            }

            // Log activity
            activity('supplier_invoices')
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'action' => 'send_to_buyer',
                ])
                ->log('قام المورد بإرسال الفاتورة للمشتري');

            return back()->with('success', 'تم إرسال الفاتورة للمشتري بنجاح');
        } catch (\Throwable $e) {
            Log::error('Supplier send invoice error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل إرسال الفاتورة: ' . $e->getMessage()]);
        }
    }
}

