<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure the user is an internal user (Admin/Staff)
 * 
 * This middleware checks if the user:
 * 1. Has Admin or Staff role, OR
 * 2. Has any admin permission assigned directly
 * 
 * This replaces the strict role:Admin middleware to support
 * permission-based authorization where roles are categories only.
 */
class EnsureInternalUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has Admin or Staff role
        if ($user->hasRole(['Admin', 'Staff'])) {
            return $next($request);
        }

        // Check if user has any admin permissions (direct permissions)
        // This allows users without Admin role but with specific permissions
        $adminPermissions = $user->permissions()
            ->where('name', 'like', '%.%')
            ->whereNotIn('name', [
                // Exclude supplier/buyer permissions
                'suppliers.%', 'buyers.%'
            ])
            ->exists();

        if ($adminPermissions) {
            return $next($request);
        }

        // Check if user has any internal user type
        if ($user->type && in_array($user->type->name, ['مدير النظام', 'موظف'])) {
            return $next($request);
        }

        // Access denied
        abort(403, 'ليس لديك صلاحية الوصول. يجب أن تكون موظفاً داخلياً.');
    }
}

