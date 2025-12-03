<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BuyerDashboardController extends Controller
{
    /**
     * Display the buyer dashboard.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure user is authenticated
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Load buyer profile
        $user->load('buyerProfile');

        // Verify user has buyer profile
        if (!$user->buyerProfile) {
            abort(403, 'Buyer profile not found');
        }

        // Check verification status
        if ($user->buyerProfile->rejection_reason) {
            return redirect()->route('auth.waiting-approval')
                ->with('message', 'تم رفض طلب تسجيلك. يرجى مراجعة سبب الرفض أدناه.');
        }

        if (!$user->buyerProfile->is_verified) {
            return redirect()->route('auth.waiting-approval')
                ->with('message', 'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة.');
        }

        // Return buyer dashboard view
        return view('buyer.dashboard');
    }
}
