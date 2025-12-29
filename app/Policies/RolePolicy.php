<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('roles.view', 'web');
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('roles.view', 'web');
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('roles.create', 'web');
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('roles.update', 'web');
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('roles.delete', 'web');
    }
}

