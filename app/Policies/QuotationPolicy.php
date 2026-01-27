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
        
        // Gate::before() handles Admin bypass
        // Staff users need explicit permission
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

        // Gate::before() handles Admin bypass
        // Staff users need explicit permission
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
        
        // Gate::before() handles Admin bypass
        // Staff users need explicit permission (rare case)
        return $user->can('quotations.submit');
    }

    /**
     * Determine if the user can update the quotation.
     * 
     * REFACTORED: Policy now checks ONLY authorization (ownership).
     * Business rules (deadline, status, etc.) are checked by QuotationStateMachine.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        // Supplier can only update their own quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $quotation->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff should NOT update quotations (violates business requirement)
        // Quotations belong to suppliers only
        return false;
    }

    /**
     * Determine if the user can delete the quotation.
     * 
     * REFACTORED: Policy checks ownership only.
     * Status validation handled by QuotationStateMachine.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        // Supplier can only delete their own quotations
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $quotation->supplier_id === $user->supplierProfile->id;
        }

        // Gate::before() handles Admin bypass
        // Staff users need explicit permission
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

        // Gate::before() handles Admin bypass
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

        // Gate::before() handles Admin bypass
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
        
        // Gate::before() handles Admin bypass
        // Staff users need explicit permission
        return $user->can('quotations.compare');
    }
}

