<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    /**
     * Determine if the user can view any suppliers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('suppliers.view');
    }

    /**
     * Determine if the user can view the supplier.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        // Suppliers can view their own profile
        if ($user->hasRole('Supplier') && $user->supplierProfile && $supplier->id === $user->supplierProfile->id) {
            return true;
        }

        return $user->can('suppliers.view');
    }

    /**
     * Determine if the user can create suppliers.
     */
    public function create(User $user): bool
    {
        return $user->can('suppliers.create');
    }

    /**
     * Determine if the user can update the supplier.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        // Supplier can update their own profile
        if ($user->hasRole('Supplier') && $user->supplierProfile && $supplier->id === $user->supplierProfile->id) {
            return true;
        }

        return $user->can('suppliers.update');
    }

    /**
     * Determine if the user can delete the supplier.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can('suppliers.delete');
    }

    /**
     * Determine if the user can verify the supplier.
     */
    public function verify(User $user, Supplier $supplier): bool
    {
        return $user->can('suppliers.verify');
    }

    /**
     * Determine if the user can toggle supplier active status.
     */
    public function toggleActive(User $user, Supplier $supplier): bool
    {
        return $user->can('suppliers.toggle_active');
    }
}

