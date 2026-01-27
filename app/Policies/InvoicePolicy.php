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
        // Buyers can always view their invoices list
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return true;
        }

        // Suppliers can always view their invoices list
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return true;
        }

        // Gate::before() handles Admin bypass
        // Staff users need explicit permission
        return $user->can('invoices.view');
    }

    /**
     * Determine if the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Buyer can view invoices for their orders
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $invoice->order && $invoice->order->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view invoices for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $invoice->order && $invoice->order->supplier_id === $user->supplierProfile->id;
        }

        // Gate::before() handles Admin bypass
        return $user->can('invoices.view');
    }

    /**
     * Determine if the user can create invoices.
     */
    public function create(User $user): bool
    {
        // Suppliers can create invoices for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return true;
        }

        // Gate::before() handles Admin bypass
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

        // Gate::before() handles Admin bypass
        return true;
    }

    /**
     * Determine if the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Suppliers cannot delete invoices (only admin can)
        if ($user->hasRole('Supplier')) {
            return false;
        }

        // Gate::before() handles Admin bypass
        return $user->can('invoices.delete');
    }

    /**
     * Determine if the user can cancel the invoice.
     */
    public function cancel(User $user, Invoice $invoice): bool
    {
        // Suppliers can cancel their invoices if not approved and not paid
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($invoice->order && $invoice->order->supplier_id === $user->supplierProfile->id) {
                // Can cancel if not approved and no payments
                return in_array($invoice->status, ['draft', 'issued']) 
                    && $invoice->payment_status === Invoice::PAYMENT_UNPAID;
            }
        }

        // Gate::before() handles Admin bypass
        return $user->can('invoices.update');
    }

    /**
     * Determine if the user can approve the invoice.
     */
    public function approve(User $user, Invoice $invoice): bool
    {
        // Gate::before() handles Admin bypass
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
        // Gate::before() handles Admin bypass
        return $user->can('invoices.export');
    }
}

