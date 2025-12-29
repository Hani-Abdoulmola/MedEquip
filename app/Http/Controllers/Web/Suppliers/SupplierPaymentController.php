<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Supplier Payment Controller
 *
 * Handles payment viewing for suppliers.
 * Suppliers can view payments related to their orders/invoices.
 */
class SupplierPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensure_supplier_profile');
    }

    /**
     * Display list of payments for the supplier.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $query = Payment::with(['invoice', 'order.buyer', 'buyer', 'processor'])
            ->where('supplier_id', $supplier->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('invoice', fn($sub) => $sub->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        $payments = $query->latest('paid_at')->latest('created_at')->paginate(15)->withQueryString();

        // Optimized stats calculation using single query
        $stats = Payment::where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(amount), 0) as total_amount,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as refunded,
                COALESCE(SUM(CASE WHEN status = ? THEN amount ELSE 0 END), 0) as completed_amount
            ', [
                'pending',
                'completed',
                'failed',
                'refunded',
                'completed',
            ])
            ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'pending' => $stats->pending ?? 0,
            'completed' => $stats->completed ?? 0,
            'failed' => $stats->failed ?? 0,
            'refunded' => $stats->refunded ?? 0,
            'completed_amount' => $stats->completed_amount ?? 0,
        ];

        // Log activity
        activity('supplier_payments')
            ->causedBy(Auth::user())
            ->withProperties([
                'supplier_id' => $supplier->id,
                'filters' => $request->only(['status', 'method', 'currency', 'date_from', 'date_to', 'search']),
            ])
            ->log('عرض المورد قائمة المدفوعات');

        return view('supplier.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display payment details.
     */
    public function show(Payment $payment): View
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Check if payment belongs to supplier
        if ($payment->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الدفعة');
        }

        $payment->load([
            'invoice.order.items.product',
            'order.items.product',
            'order.buyer',
            'buyer',
            'processor',
        ]);

        // Get payment receipts
        $receipts = $payment->getMedia('payment_receipts');

        // Log activity
        activity('supplier_payments')
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->withProperties([
                'payment_reference' => $payment->payment_reference,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'method' => $payment->method,
            ])
            ->log('عرض المورد تفاصيل الدفعة: ' . $payment->payment_reference);

        return view('supplier.payments.show', compact('payment', 'receipts'));
    }
}

