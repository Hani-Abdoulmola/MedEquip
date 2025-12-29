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
        return $user->hasAnyRole(['Admin', 'Buyer', 'Supplier']);
    }

    /**
     * Determine if the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Admin can view all payments
        if ($user->hasRole('Admin')) {
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
        // Admin and buyers can create payments
        return $user->hasAnyRole(['Admin', 'Buyer']);
    }

    /**
     * Determine if the user can update the payment.
     */
    public function update(User $user, Payment $payment): bool
    {
        // Only admin can update payments
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can delete the payment.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Only admin can delete payments
        return $user->hasRole('Admin');
    }
}

