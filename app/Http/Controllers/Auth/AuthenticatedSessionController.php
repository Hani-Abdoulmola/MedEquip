<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ✅ Check if user is supplier or buyer and needs approval
        $user = auth()->user();

        // If user has supplier profile, check verification status
        if ($user->supplierProfile) {
            if (!$user->supplierProfile->is_verified) {
                return redirect()->route('auth.waiting-approval');
            }
        }

        // If user has buyer profile, check verification status
        if ($user->buyerProfile) {
            if (!$user->buyerProfile->is_verified) {
                return redirect()->route('auth.waiting-approval');
            }
        }

        // Admin users or verified suppliers/buyers can proceed to dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
