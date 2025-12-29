<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Exports\SupplierInvoicesExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
}

