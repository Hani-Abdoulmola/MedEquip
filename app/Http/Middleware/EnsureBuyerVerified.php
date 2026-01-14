<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure buyer is verified and active before accessing buyer routes.
 * 
 * This middleware checks:
 * 1. User has a buyer profile
 * 2. Buyer profile is verified (is_verified = true)
 * 3. Buyer profile is active (is_active = true)
 */
class EnsureBuyerVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $buyer = $user->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        // Check if buyer has been rejected
        if ($buyer->rejection_reason) {
            return redirect()->route('auth.waiting-approval')
                ->with('error', 'تم رفض طلب تسجيلك. السبب: ' . $buyer->rejection_reason);
        }

        // Check if buyer is verified
        if (!$buyer->is_verified) {
            return redirect()->route('auth.waiting-approval')
                ->with('message', 'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة.');
        }

        // Check if buyer is active
        if (!$buyer->is_active) {
            return redirect()->route('auth.waiting-approval')
                ->with('error', 'تم تعطيل حسابك. يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}

