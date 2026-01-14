<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    /**
     * Determine if the user can view any deliveries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Buyer', 'Supplier']);
    }

    /**
     * Determine if the user can view the delivery.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        // Admin can view all deliveries
        if ($user->can('deliveries.view')) {
            return true;
        }

        // Buyer can view deliveries for their orders
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Check via order relationship (more reliable)
            if ($delivery->order && $delivery->order->buyer_id === $user->buyerProfile->id) {
                return true;
            }
            // Also check direct buyer_id
            return $delivery->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view deliveries for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            // Check via order relationship (more reliable)
            if ($delivery->order && $delivery->order->supplier_id === $user->supplierProfile->id) {
                return true;
            }
            // Also check direct supplier_id
            return $delivery->supplier_id === $user->supplierProfile->id;
        }

        return false;
    }

    /**
     * Determine if the user can create deliveries.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Supplier']);
    }

    /**
     * Determine if the user can update the delivery.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        // Admin can update any delivery
        if ($user->can('deliveries.update')) {
            return true;
        }

        // Supplier can update deliveries for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $delivery->supplier_id === $user->supplierProfile->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the delivery.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        // Only admin can delete deliveries
        return $user->can('deliveries.delete');
    }

    /**
     * Determine if the user can update delivery status.
     */
    public function updateStatus(User $user, Delivery $delivery): bool
    {
        // Admin can update any delivery status
        if ($user->can('deliveries.update_status')) {
            return true;
        }

        // Supplier can update status for their deliveries
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $delivery->supplier_id === $user->supplierProfile->id;
        }

        return false;
    }

    /**
     * Determine if the user can verify the delivery.
     */
    public function verify(User $user, Delivery $delivery): bool
    {
        // Only admin can verify deliveries
        return $user->can('deliveries.verify');
    }

    /**
     * Determine if the user can upload delivery proof.
     */
    public function uploadProof(User $user, Delivery $delivery): bool
    {
        // Supplier can upload proof for their deliveries
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $delivery->supplier_id === $user->supplierProfile->id;
        }

        return false;
    }

    /**
     * Determine if the user can confirm delivery receipt.
     * 
     * Only buyers can confirm receipt of their deliveries.
     */
    public function confirmReceipt(User $user, Delivery $delivery): bool
    {
        // Buyer can confirm receipt for their deliveries
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Check via order relationship (more reliable for some delivery records)
            if ($delivery->order && $delivery->order->buyer_id === $user->buyerProfile->id) {
                return true;
            }
            
            // Also check direct buyer_id
            return $delivery->buyer_id === $user->buyerProfile->id;
        }

        return false;
    }
}

