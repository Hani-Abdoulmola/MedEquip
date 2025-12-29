<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure user has a supplier profile.
 * Aborts with 403 if supplier profile doesn't exist.
 */
class EnsureSupplierProfile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->supplierProfile) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        return $next($request);
    }
}

