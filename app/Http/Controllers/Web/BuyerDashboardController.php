<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BuyerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BuyerDashboardController extends Controller
{
    protected BuyerService $buyerService;

    public function __construct(BuyerService $buyerService)
    {
        $this->buyerService = $buyerService;
    }

    /**
     * Display the buyer dashboard.
     * 
     * Note: Buyer verification is handled by the 'buyer.verified' middleware.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $buyer = $user->buyerProfile;

        // Get dashboard statistics
        $stats = $this->buyerService->getDashboardStats($buyer);

        // Get recent RFQs
        $recentRfqs = $this->buyerService->getRecentRfqs($buyer, 5);

        // Get recent quotations
        $recentQuotations = $this->buyerService->getRecentQuotations($buyer, 5);

        // Get pending quotations for review
        $pendingQuotations = $this->buyerService->getPendingQuotationsForReview($buyer);

        // Get spending trend for chart
        $spendingTrend = $this->buyerService->getSpendingTrend($buyer, 7);

        // Get RFQ status distribution for chart
        $rfqDistribution = $this->buyerService->getRfqStatusDistribution($buyer);

        // Get upcoming events
        $upcomingEvents = $this->buyerService->getUpcomingEvents($buyer);

        // Return buyer dashboard view with real data
        return view('buyer.dashboard', compact(
            'buyer',
            'stats',
            'recentRfqs',
            'recentQuotations',
            'pendingQuotations',
            'spendingTrend',
            'rfqDistribution',
            'upcomingEvents'
        ));
    }
}
