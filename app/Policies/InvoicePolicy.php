<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine if the user can view any invoices.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('invoices.view');
    }

    /**
     * Determine if the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        if (!$user->can('invoices.view')) {
            return false;
        }

        // Buyer can view invoices for their orders
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $invoice->order && $invoice->order->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view invoices for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $invoice->order && $invoice->order->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff with permission can view all
        return true;
    }

    /**
     * Determine if the user can create invoices.
     */
    public function create(User $user): bool
    {
        return $user->can('invoices.create');
    }

    /**
     * Determine if the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        if (!$user->can('invoices.update')) {
            return false;
        }

        // Supplier can update invoices for their orders (if status allows)
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($invoice->order && $invoice->order->supplier_id === $user->supplierProfile->id) {
                // Can only update if invoice is not approved
                return in_array($invoice->status, ['draft', 'issued']);
            }
        }

        // Admin/Staff with permission can update any invoice
        return true;
    }

    /**
     * Determine if the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.delete');
    }

    /**
     * Determine if the user can approve the invoice.
     */
    public function approve(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.approve');
    }

    /**
     * Determine if the user can download the invoice.
     */
    public function download(User $user, Invoice $invoice): bool
    {
        // Same as view permission
        return $this->view($user, $invoice);
    }

    /**
     * Determine if the user can export invoices.
     */
    public function export(User $user): bool
    {
        return $user->can('invoices.export');
    }
}

