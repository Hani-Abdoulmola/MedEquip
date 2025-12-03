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
     *
     * Login Logic:
     * - Admins: Can always login (no verification needed)
     * - Suppliers/Buyers: Can login ONLY if is_verified = true
     *   - is_verified = true when:
     *     1. Admin approves their registration request, OR
     *     2. Admin manually creates them through control panel
     *   - is_verified = false when:
     *     - User self-registers (needs admin approval)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'فشل تسجيل الدخول']);
        }

        // Load relationships to avoid N+1 queries
        $user->load(['supplierProfile', 'buyerProfile']);

        // ✅ Check supplier verification status
        // Suppliers can login ONLY if admin has verified them (is_verified = true)
        // This happens when:
        // 1. Admin approves their registration request via RegistrationApprovalController
        // 2. Admin manually creates them via SupplierController (is_verified defaults to true)
        if ($user->supplierProfile) {
            // If rejected, redirect to waiting-approval to see rejection reason
            if ($user->supplierProfile->rejection_reason) {
                return redirect()->route('auth.waiting-approval')
                    ->with('message', 'تم رفض طلب تسجيلك. يرجى مراجعة سبب الرفض أدناه.');
            }
            // If not verified (pending), redirect to waiting-approval
            if (!$user->supplierProfile->is_verified) {
                return redirect()->route('auth.waiting-approval')
                    ->with('message', 'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة.');
            }
        }

        // ✅ Check buyer verification status
        // Buyers can login ONLY if admin has verified them (is_verified = true)
        // This happens when:
        // 1. Admin approves their registration request via RegistrationApprovalController
        // 2. Admin manually creates them via BuyerController (is_verified defaults to true)
        if ($user->buyerProfile) {
            // If rejected, redirect to waiting-approval to see rejection reason
            if ($user->buyerProfile->rejection_reason) {
                return redirect()->route('auth.waiting-approval')
                    ->with('message', 'تم رفض طلب تسجيلك. يرجى مراجعة سبب الرفض أدناه.');
            }
            // If not verified (pending), redirect to waiting-approval
            if (!$user->buyerProfile->is_verified) {
                return redirect()->route('auth.waiting-approval')
                    ->with('message', 'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة.');
            }
        }

        // ✅ Redirect to appropriate dashboard based on user type
        $user->load(['supplierProfile', 'buyerProfile']);
        
        if ($user->supplierProfile) {
            return redirect()->intended(route('supplier.dashboard', absolute: false));
        }
        
        if ($user->buyerProfile) {
            return redirect()->intended(route('buyer.dashboard', absolute: false));
        }
        
        // Admin users go to main dashboard
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
