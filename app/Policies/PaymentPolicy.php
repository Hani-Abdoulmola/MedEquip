<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine if the user can view any payments.
     */
    public function viewAny(User $user): bool
    {
        // Gate::before() handles Admin bypass
        // Buyers and Suppliers can view their own payments
        if ($user->hasAnyRole(['Buyer', 'Supplier'])) {
            return true;
        }
        
        // Staff users need explicit permission
        return $user->can('payments.view');
    }

    /**
     * Determine if the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Gate::before() handles Admin bypass
        // Staff users with permission can view all payments
        if ($user->can('payments.view')) {
            return true;
        }

        // Buyer can view payments for their orders/invoices
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $payment->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view payments for their orders/invoices
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $payment->supplier_id === $user->supplierProfile->id;
        }

        return false;
    }

    /**
     * Determine if the user can create payments.
     */
    public function create(User $user): bool
    {
        // Gate::before() handles Admin bypass
        // Buyers can create payments
        if ($user->hasRole('Buyer')) {
            return true;
        }
        
        // Staff users need explicit permission
        return $user->can('payments.create');
    }

    /**
     * Determine if the user can update the payment.
     */
    public function update(User $user, Payment $payment): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('payments.update');
    }

    /**
     * Determine if the user can delete the payment.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('payments.delete');
    }
}

