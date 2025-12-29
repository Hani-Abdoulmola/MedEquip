<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('permissions.view', 'web');
    }

    /**
     * Determine if the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        // Admin role always has full access
        return $user->hasRole('Admin') || $user->hasPermissionTo('permissions.view', 'web');
    }
}

