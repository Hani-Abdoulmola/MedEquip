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
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Buyer can view deliveries for their orders
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $delivery->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view deliveries for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
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
        if ($user->hasRole('Admin')) {
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
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can update delivery status.
     */
    public function updateStatus(User $user, Delivery $delivery): bool
    {
        // Admin can update any delivery status
        if ($user->hasRole('Admin')) {
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
        return $user->hasRole('Admin');
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
}

