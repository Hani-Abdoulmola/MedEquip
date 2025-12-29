<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(): View
    {
        // Direct role check since policy resolution has issues
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        // Group permissions by module
        $permissions = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                // Extract module from permission name (e.g., 'users.view' -> 'users')
                return explode('.', $permission->name)[0];
            });

        // Get role counts for each permission
        $permissionRoleCounts = [];
        foreach (Permission::all() as $permission) {
            $permissionRoleCounts[$permission->id] = $permission->roles()->count();
        }

        return view('admin.permissions.index', compact('permissions', 'permissionRoleCounts'));
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): View
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $permission->load(['roles', 'users']);

        return view('admin.permissions.show', compact('permission'));
    }
}

