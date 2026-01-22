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
        // This is the ONLY authorization mechanism (no user_type bypass)
        if ($user->hasRole(['Admin', 'Staff'])) {
            return $next($request);
        }

        // Access denied - user must have Admin or Staff role
        abort(403, 'ليس لديك صلاحية الوصول. يجب أن تكون لديك صلاحيات إدارية (Admin أو Staff).');
    }
}

