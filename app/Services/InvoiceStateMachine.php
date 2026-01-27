<?php

namespace App\Services;

use App\Models\Invoice;

/**
 * Invoice State Machine
 * 
 * Manages invoice status transitions and validates business rules.
 */
class InvoiceStateMachine
{
    /**
     * Allowed status transitions
     */
    private const TRANSITIONS = [
        Invoice::STATUS_DRAFT => [Invoice::STATUS_ISSUED, Invoice::STATUS_CANCELLED],
        Invoice::STATUS_ISSUED => [Invoice::STATUS_APPROVED, Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT], // Can go back to draft
        Invoice::STATUS_APPROVED => [Invoice::STATUS_CANCELLED], // Can only cancel after approval
        Invoice::STATUS_CANCELLED => [], // Terminal state
    ];

    /**
     * Check if invoice can transition to a new status
     * 
     * @param Invoice $invoice
     * @param string $toStatus
     * @return bool
     */
    public function canTransition(Invoice $invoice, string $toStatus): bool
    {
        // Get allowed transitions for current state
        $allowedStates = self::TRANSITIONS[$invoice->status] ?? [];

        if (!in_array($toStatus, $allowedStates)) {
            return false;
        }

        // Additional business rule validation
        return match($toStatus) {
            Invoice::STATUS_ISSUED => $this->canIssue($invoice),
            Invoice::STATUS_APPROVED => $this->canApprove($invoice),
            Invoice::STATUS_CANCELLED => $this->canCancel($invoice),
            Invoice::STATUS_DRAFT => $this->canRevertToDraft($invoice),
            default => false,
        };
    }

    /**
     * Check if invoice can be issued
     */
    private function canIssue(Invoice $invoice): bool
    {
        // Can issue if in draft status
        return $invoice->status === Invoice::STATUS_DRAFT;
    }

    /**
     * Check if invoice can be approved
     */
    private function canApprove(Invoice $invoice): bool
    {
        // Can approve if issued
        if ($invoice->status !== Invoice::STATUS_ISSUED) {
            return false;
        }

        // Cannot approve if already cancelled
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return false;
        }

        return true;
    }

    /**
     * Check if invoice can be cancelled
     */
    private function canCancel(Invoice $invoice): bool
    {
        // Cannot cancel if fully paid
        if ($invoice->payment_status === Invoice::PAYMENT_PAID) {
            return false;
        }

        // Cannot cancel if partially paid (may need admin approval)
        if ($invoice->payment_status === Invoice::PAYMENT_PARTIAL) {
            // Allow cancellation but may need special handling
            return true;
        }

        return true;
    }

    /**
     * Check if invoice can revert to draft
     */
    private function canRevertToDraft(Invoice $invoice): bool
    {
        // Can revert from issued to draft if not paid
        return $invoice->status === Invoice::STATUS_ISSUED 
            && $invoice->payment_status === Invoice::PAYMENT_UNPAID;
    }

    /**
     * Transition invoice to new status
     * 
     * @param Invoice $invoice
     * @param string $toStatus
     * @param array $additionalData
     * @return bool
     */
    public function transition(Invoice $invoice, string $toStatus, array $additionalData = []): bool
    {
        if (!$this->canTransition($invoice, $toStatus)) {
            return false;
        }

        $oldStatus = $invoice->status;

        // Update status
        $invoice->status = $toStatus;

        // Handle status-specific updates
        match($toStatus) {
            Invoice::STATUS_APPROVED => $this->handleApproval($invoice, $additionalData),
            Invoice::STATUS_CANCELLED => $this->handleCancellation($invoice, $additionalData),
            default => null,
        };

        $invoice->save();

        return true;
    }

    /**
     * Handle invoice approval
     */
    private function handleApproval(Invoice $invoice, array $data): void
    {
        if (isset($data['approved_by'])) {
            $invoice->approved_by = $data['approved_by'];
        }
    }

    /**
     * Handle invoice cancellation
     */
    private function handleCancellation(Invoice $invoice, array $data): void
    {
        if (isset($data['cancellation_reason'])) {
            $invoice->notes = ($invoice->notes ? $invoice->notes . "\n\n" : '')
                . 'تم الإلغاء: ' . $data['cancellation_reason'];
        }
    }
}
