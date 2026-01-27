<?php

namespace App\Policies;

use App\Models\Manufacturer;
use App\Models\User;

class ManufacturerPolicy
{
    /**
     * Determine if the user can view any manufacturers.
     */
    public function viewAny(User $user): bool
    {
        return true; // Manufacturers are viewable by all authenticated users
    }

    /**
     * Determine if the user can view the manufacturer.
     */
    public function view(User $user, Manufacturer $manufacturer): bool
    {
        return true; // All authenticated users can view manufacturers
    }

    /**
     * Determine if the user can create manufacturers.
     */
    public function create(User $user): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('manufacturers.create');
    }

    /**
     * Determine if the user can update the manufacturer.
     */
    public function update(User $user, Manufacturer $manufacturer): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('manufacturers.update');
    }

    /**
     * Determine if the user can delete the manufacturer.
     */
    public function delete(User $user, Manufacturer $manufacturer): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('manufacturers.delete');
    }
}

