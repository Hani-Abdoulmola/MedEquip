<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('orders.view');
    }

    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        if (!$user->can('orders.view')) {
            return false;
        }

        // Buyer can view their own orders
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            return $order->buyer_id === $user->buyerProfile->id;
        }

        // Supplier can view orders assigned to them
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $order->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff with permission can view all
        return true;
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->can('orders.create');
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->can('orders.update');
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->can('orders.delete');
    }

    /**
     * Determine if the user can confirm the order.
     */
    public function confirm(User $user, Order $order): bool
    {
        return $user->can('orders.confirm');
    }

    /**
     * Determine if the user can update order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        if (!$user->can('orders.update_status')) {
            return false;
        }

        // Supplier can update status for their orders
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $order->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff with permission can update any order status
        return true;
    }
}

