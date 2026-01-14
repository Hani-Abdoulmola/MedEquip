<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Base Controller for Web Controllers
 * 
 * Provides helper methods for common authorization and view selection logic.
 */
abstract class BaseController extends Controller
{
    /**
     * Check if the current user is an admin (has admin permissions).
     * 
     * This is more flexible than hasRole('Admin') because it checks permissions
     * rather than just the role name.
     * 
     * @param string|null $permission Optional permission to check (e.g., 'invoices.view')
     * @return bool
     */
    protected function isAdmin(?string $permission = null): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // If specific permission provided, check that
        if ($permission) {
            return $user->can($permission);
        }

        // Otherwise, check if user has admin role or admin-level permissions
        // Admin role typically has all permissions, so we check for a common admin permission
        return $user->hasRole('Admin') || $user->can('users.view');
    }

    /**
     * Get the appropriate view name based on user role.
     * 
     * @param string $adminView View name for admin users
     * @param string $defaultView View name for non-admin users
     * @param string|null $permission Optional permission to check instead of role
     * @return string
     */
    protected function getView(string $adminView, string $defaultView, ?string $permission = null): string
    {
        return $this->isAdmin($permission) ? $adminView : $defaultView;
    }
}

