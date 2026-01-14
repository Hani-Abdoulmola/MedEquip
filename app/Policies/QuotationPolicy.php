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
        // Suppliers can view their quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return true;
        }
        
        // Buyers can view quotations for their RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return true;
        }
        
        // Admin/Staff need permission
        return $user->can('quotations.view');
    }

    /**
     * Determine if the user can view the quotation.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        // Buyer can view quotations for their RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $quotation->rfq && $quotation->rfq->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view their own quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $quotation->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff need permission
        return $user->can('quotations.view');
    }

    /**
     * Determine if the user can create quotations.
     */
    public function create(User $user): bool
    {
        // Only suppliers can create quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return true;
        }
        
        // Admin/Staff need permission (rare case)
        return $user->can('quotations.submit');
    }

    /**
     * Determine if the user can update the quotation.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        // Supplier can only update their own pending quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($quotation->supplier_id !== $user->supplierProfile->id) {
                return false;
            }
            
            // Can only update if status is pending and RFQ is still open
            if ($quotation->status !== 'pending') {
                return false;
            }
            
            // Check if RFQ deadline has passed
            if ($quotation->rfq->deadline && $quotation->rfq->deadline->isPast()) {
                return false;
            }
            
            return $quotation->rfq && $quotation->rfq->status === 'open';
        }

        // Admin should NOT update quotations (violates requirement)
        // Keep for emergency cases only, but should be restricted
        return false;
    }

    /**
     * Determine if the user can delete the quotation.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        // Supplier can only delete their own pending quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($quotation->supplier_id !== $user->supplierProfile->id) {
                return false;
            }
            
            return $quotation->status === 'pending';
        }

        // Admin/Staff need permission
        return $user->can('quotations.delete');
    }

    /**
     * Determine if the user can accept the quotation.
     */
    public function accept(User $user, Quotation $quotation): bool
    {
        // Buyer can accept quotations for their own RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $quotation->rfq && $quotation->rfq->buyer_id === $user->buyerProfile->id;
        }

        // Admin/Staff need permission
        return $user->can('quotations.accept');
    }

    /**
     * Determine if the user can reject the quotation.
     */
    public function reject(User $user, Quotation $quotation): bool
    {
        // Buyer can reject quotations for their own RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $quotation->rfq && $quotation->rfq->buyer_id === $user->buyerProfile->id;
        }

        // Admin/Staff need permission
        return $user->can('quotations.reject');
    }

    /**
     * Determine if the user can compare quotations.
     */
    public function compare(User $user): bool
    {
        // Buyers can compare quotations for their RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return true;
        }
        
        // Admin/Staff need permission
        return $user->can('quotations.compare');
    }
}

