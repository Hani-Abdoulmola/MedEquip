<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTracking;
use App\Models\DeliveryDispute;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BuyerDeliveryTrackingController extends Controller
{
    /**
     * Display tracking for an order.
     */
    public function show(Order $order): View
    {
        $buyer = auth()->user()->buyerProfile;
        
        if ($order->buyer_id !== $buyer->id) {
            abort(403);
        }

        $order->load(['delivery.tracking', 'supplier', 'items.product']);

        return view('buyer.deliveries.tracking', compact('order'));
    }

    /**
     * Show delivery calendar with all upcoming deliveries.
     */
    public function calendar(): View
    {
        $buyer = auth()->user()->buyerProfile;

        $deliveries = DeliveryTracking::whereHas('order', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
            ->whereIn('status', [
                DeliveryTracking::STATUS_CONFIRMED,
                DeliveryTracking::STATUS_PREPARING,
                DeliveryTracking::STATUS_SHIPPED,
                DeliveryTracking::STATUS_IN_TRANSIT,
                DeliveryTracking::STATUS_OUT_FOR_DELIVERY,
            ])
            ->with(['order', 'delivery'])
            ->orderBy('estimated_delivery_at')
            ->get();

        return view('buyer.deliveries.calendar', compact('deliveries'));
    }

    /**
     * Create a delivery dispute.
     */
    public function createDispute(Order $order): View
    {
        $buyer = auth()->user()->buyerProfile;
        
        if ($order->buyer_id !== $buyer->id) {
            abort(403);
        }

        $order->load('delivery');

        return view('buyer.deliveries.create-dispute', compact('order'));
    }

    /**
     * Store a new delivery dispute.
     */
    public function storeDispute(Request $request, Order $order): RedirectResponse
    {
        $buyer = auth()->user()->buyerProfile;
        
        if ($order->buyer_id !== $buyer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:not_delivered,late_delivery,damaged_products,wrong_products,missing_items,quality_issue,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);

        $validated['delivery_id'] = $order->delivery->id;
        $validated['order_id'] = $order->id;
        $validated['buyer_id'] = $buyer->id;
        $validated['supplier_id'] = $order->supplier_id;
        $validated['status'] = DeliveryDispute::STATUS_OPEN;
        $validated['priority'] = 'medium';

        // Handle photo uploads (simplified - would use media library in production)
        if ($request->hasFile('photos')) {
            $validated['photos'] = []; // Store photo paths
        }

        $dispute = DeliveryDispute::create($validated);

        activity()
            ->performedOn($dispute)
            ->causedBy(auth()->user())
            ->log('Delivery dispute created');

        return redirect()
            ->route('buyer.deliveries.dispute', $dispute)
            ->with('success', 'تم تقديم النزاع بنجاح. سنقوم بالتواصل معك قريباً.');
    }

    /**
     * Show dispute details.
     */
    public function showDispute(DeliveryDispute $dispute): View
    {
        $buyer = auth()->user()->buyerProfile;
        
        if ($dispute->buyer_id !== $buyer->id) {
            abort(403);
        }

        $dispute->load(['order', 'delivery', 'supplier']);

        return view('buyer.deliveries.dispute-details', compact('dispute'));
    }

    /**
     * List all buyer's disputes.
     */
    public function disputes(): View
    {
        $buyer = auth()->user()->buyerProfile;

        $disputes = DeliveryDispute::where('buyer_id', $buyer->id)
            ->with(['order', 'delivery', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('buyer.deliveries.disputes', compact('disputes'));
    }
}
