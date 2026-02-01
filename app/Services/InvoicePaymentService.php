<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

/**
 * Invoice Payment Service
 * 
 * Handles automatic synchronization of invoice payment status
 * based on payment records.
 */
class InvoicePaymentService
{
    /**
     * Refresh invoice payment status based on payments
     * 
     * @param Invoice $invoice
     * @return void
     */
    public function refreshPaymentStatus(Invoice $invoice): void
    {
        try {
            // Reload payments to ensure we have latest data
            $invoice->load('payments');

            // Calculate total paid (only completed payments)
            $totalPaid = (float) $invoice->payments()
                ->where('status', 'completed')
                ->sum('amount');

            $invoiceTotal = (float) $invoice->total_amount;

            // Determine payment status
            if ($totalPaid >= $invoiceTotal) {
                $newPaymentStatus = Invoice::PAYMENT_PAID;
            } elseif ($totalPaid > 0) {
                $newPaymentStatus = Invoice::PAYMENT_PARTIAL;
            } else {
                $newPaymentStatus = Invoice::PAYMENT_UNPAID;
            }

            // Update if changed
            if ($invoice->payment_status !== $newPaymentStatus) {
                $oldStatus = $invoice->payment_status;
                $invoice->payment_status = $newPaymentStatus;
                $invoice->save();

                // Log the change
                activity('invoice_payments')
                    ->performedOn($invoice)
                    ->withProperties([
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'old_payment_status' => $oldStatus,
                        'new_payment_status' => $newPaymentStatus,
                        'total_paid' => $totalPaid,
                        'invoice_total' => $invoiceTotal,
                    ])
                    ->log('تم تحديث حالة الدفع للفاتورة تلقائياً');
            }
        } catch (\Throwable $e) {
            Log::error('Invoice payment status refresh failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate remaining balance for invoice
     * 
     * @param Invoice $invoice
     * @return float
     */
    public function getRemainingBalance(Invoice $invoice): float
    {
        $totalPaid = (float) $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

        return max(0, (float) $invoice->total_amount - $totalPaid);
    }

    /**
     * Check if invoice is overdue
     * 
     * @param Invoice $invoice
     * @param int $daysPastDue Number of days to consider overdue (default: 30)
     * @return bool
     */
    public function isOverdue(Invoice $invoice, int $daysPastDue = 30): bool
    {
        if ($invoice->payment_status === Invoice::PAYMENT_PAID) {
            return false;
        }

        // Check if invoice date is more than X days ago
        if (!$invoice->invoice_date) {
            return false;
        }

        return $invoice->invoice_date->addDays($daysPastDue)->isPast();
    }

    /**
     * Send payment reminder notification
     * 
     * @param Invoice $invoice
     * @return void
     */
    public function sendPaymentReminder(Invoice $invoice): void
    {
        if ($invoice->payment_status === Invoice::PAYMENT_PAID) {
            return;
        }

        if ($invoice->order && $invoice->order->buyer && $invoice->order->buyer->user) {
            $remainingBalance = $this->getRemainingBalance($invoice);

            NotificationService::send(
                $invoice->order->buyer->user,
                '⏰ تذكير بدفع الفاتورة',
                "فاتورة رقم {$invoice->invoice_number} بقيمة {$remainingBalance} د.ل. متبقية للدفع.",
                route('buyer.invoices.show', $invoice->id)
            );
        }
    }
}
