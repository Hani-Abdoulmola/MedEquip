<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\User;

class RfqPolicy
{
    /**
     * Determine if the user can view any RFQs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('rfqs.view');
    }

    /**
     * Determine if the user can view the RFQ.
     */
    public function view(User $user, Rfq $rfq): bool
    {
        // Check base permission
        if (!$user->can('rfqs.view')) {
            return false;
        }

        // Buyer can view their own RFQs
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $rfq->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view if:
        // - RFQ is public and supplier is verified
        // - Supplier is assigned to the RFQ
        // - Supplier has already submitted a quotation
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            $supplier = $user->supplierProfile;
            
            if ($rfq->is_public && $supplier->is_verified) {
                return true;
            }
            
            if ($rfq->isAssignedTo($supplier->id)) {
                return true;
            }
            
            if ($rfq->hasQuotationFrom($supplier->id)) {
                return true;
            }
        }

        // Admin/Staff with permission can view all
        return true;
    }

    /**
     * Determine if the user can create RFQs.
     */
    public function create(User $user): bool
    {
        return $user->can('rfqs.create');
    }

    /**
     * Determine if the user can update the RFQ.
     */
    public function update(User $user, Rfq $rfq): bool
    {
        if (!$user->can('rfqs.update')) {
            return false;
        }

        // Buyer can update their own RFQs (if status allows)
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            if ($rfq->buyer_id !== $user->buyerProfile->id) {
                return false;
            }
            
            // Buyers can only update draft or open RFQs
            return in_array($rfq->status, ['draft', 'open']);
        }

        // Admin/Staff with permission can update any RFQ
        return true;
    }

    /**
     * Determine if the user can delete the RFQ.
     */
    public function delete(User $user, Rfq $rfq): bool
    {
        if (!$user->can('rfqs.delete')) {
            return false;
        }

        // Buyer can delete their own RFQs if no quotations exist
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            if ($rfq->buyer_id !== $user->buyerProfile->id) {
                return false;
            }
            
            // Cannot delete if quotations exist
            return $rfq->quotations()->count() === 0;
        }

        // Admin/Staff with permission can delete any RFQ
        return true;
    }

    /**
     * Determine if the user can assign suppliers to the RFQ.
     */
    public function assignSuppliers(User $user, Rfq $rfq): bool
    {
        return $user->can('rfqs.assign_suppliers');
    }

    /**
     * Determine if the user can update the RFQ status.
     */
    public function updateStatus(User $user, Rfq $rfq): bool
    {
        return $user->can('rfqs.update_status');
    }

    /**
     * Determine if the user can toggle RFQ visibility.
     */
    public function toggleVisibility(User $user, Rfq $rfq): bool
    {
        return $user->can('rfqs.toggle_visibility');
    }

    /**
     * Determine if the user can create a quotation for the RFQ.
     */
    public function createQuotation(User $user, Rfq $rfq): bool
    {
        // Only suppliers can create quotations
        if (!$user->hasRole('Supplier') || !$user->supplierProfile) {
            return false;
        }

        $supplier = $user->supplierProfile;

        // RFQ must be open
        if ($rfq->status !== 'open') {
            return false;
        }

        // Supplier must be verified
        if (!$supplier->is_verified) {
            return false;
        }

        // Supplier can quote if:
        // - RFQ is public
        // - Supplier is assigned to the RFQ
        if ($rfq->is_public) {
            return true;
        }

        if ($rfq->isAssignedTo($supplier->id)) {
            return true;
        }

        return false;
    }
}

