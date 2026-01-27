<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;

class PermissionPolicy
{
    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('permissions.view');
    }

    /**
     * Determine if the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('permissions.view');
    }
}

