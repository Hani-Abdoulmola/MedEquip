<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    /**
     * Determine if the user can view any quotations.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('quotations.view');
    }

    /**
     * Determine if the user can view the quotation.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        if (!$user->can('quotations.view')) {
            return false;
        }

        // Buyer can view quotations for their RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $quotation->rfq && $quotation->rfq->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view their own quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $quotation->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff with permission can view all
        return true;
    }

    /**
     * Determine if the user can create quotations.
     */
    public function create(User $user): bool
    {
        return $user->can('quotations.submit');
    }

    /**
     * Determine if the user can update the quotation.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        if (!$user->can('quotations.update')) {
            return false;
        }

        // Supplier can only update their own pending quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($quotation->supplier_id !== $user->supplierProfile->id) {
                return false;
            }
            
            // Can only update if status is pending and RFQ is still open
            if ($quotation->status !== 'pending') {
                return false;
            }
            
            return $quotation->rfq && $quotation->rfq->status === 'open';
        }

        // Admin/Staff with permission can update any quotation
        return true;
    }

    /**
     * Determine if the user can delete the quotation.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        if (!$user->can('quotations.delete')) {
            return false;
        }

        // Supplier can only delete their own pending quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($quotation->supplier_id !== $user->supplierProfile->id) {
                return false;
            }
            
            return $quotation->status === 'pending';
        }

        // Admin/Staff with permission can delete any quotation
        return true;
    }

    /**
     * Determine if the user can accept the quotation.
     */
    public function accept(User $user, Quotation $quotation): bool
    {
        return $user->can('quotations.accept');
    }

    /**
     * Determine if the user can reject the quotation.
     */
    public function reject(User $user, Quotation $quotation): bool
    {
        return $user->can('quotations.reject');
    }

    /**
     * Determine if the user can compare quotations.
     */
    public function compare(User $user): bool
    {
        return $user->can('quotations.compare');
    }
}

