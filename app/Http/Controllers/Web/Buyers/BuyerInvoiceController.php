<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Buyer Invoice Controller
 *
 * Handles invoice viewing and downloading for buyers.
 * Buyers can view and download invoices related to their orders.
 * 
 * Note: Buyer verification is handled by the 'buyer.verified' middleware.
 */
class BuyerInvoiceController extends Controller
{
    /**
     * Display list of invoices for the buyer.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $buyer = Auth::user()->buyerProfile;

        $query = Invoice::with(['order.supplier.user'])
            ->whereHas('order', function ($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by payment status
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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('supplier', fn($s) => $s->where('company_name', 'like', "%{$search}%"));
                  });
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        // Stats calculation
        $stats = Invoice::whereHas('order', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
        ->selectRaw('
            COUNT(*) as total,
            COALESCE(SUM(total_amount), 0) as total_amount,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as paid,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as unpaid,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as partial
        ', [
            Invoice::PAYMENT_PAID,
            Invoice::PAYMENT_UNPAID,
            Invoice::PAYMENT_PARTIAL,
        ])
        ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'paid' => $stats->paid ?? 0,
            'unpaid' => $stats->unpaid ?? 0,
            'partial' => $stats->partial ?? 0,
        ];

        // Log activity
        activity('buyer_invoices')
            ->causedBy(Auth::user())
            ->withProperties([
                'buyer_id' => $buyer->id,
                'filters' => $request->only(['status', 'payment_status', 'from_date', 'to_date', 'search']),
            ])
            ->log('عرض المشتري قائمة الفواتير');

        return view('buyer.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Display invoice details.
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $buyer = Auth::user()->buyerProfile;

        $invoice->load(['order.supplier.user', 'order.items.product', 'payments']);

        // Log activity
        activity('buyer_invoices')
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ])
            ->log('عرض المشتري تفاصيل الفاتورة: ' . $invoice->invoice_number);

        return view('buyer.invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice): Response
    {
        $this->authorize('download', $invoice);

        $buyer = Auth::user()->buyerProfile;

        $invoice->load(['order.supplier.user', 'order.items.product', 'order.buyer.user']);

        $pdf = PDF::loadView('buyer.invoices.pdf', compact('invoice'));

        // Log activity
        activity('buyer_invoices')
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'action' => 'download',
            ])
            ->log('قام المشتري بتحميل الفاتورة: ' . $invoice->invoice_number);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Get payment status label in Arabic.
     */
    public static function getPaymentStatusLabel(string $status): string
    {
        return match($status) {
            Invoice::PAYMENT_PAID => 'مدفوعة',
            Invoice::PAYMENT_UNPAID => 'غير مدفوعة',
            Invoice::PAYMENT_PARTIAL => 'مدفوعة جزئياً',
            default => $status,
        };
    }

    /**
     * Get payment status color for UI.
     */
    public static function getPaymentStatusColor(string $status): string
    {
        return match($status) {
            Invoice::PAYMENT_PAID => 'green',
            Invoice::PAYMENT_UNPAID => 'red',
            Invoice::PAYMENT_PARTIAL => 'yellow',
            default => 'gray',
        };
    }
}

