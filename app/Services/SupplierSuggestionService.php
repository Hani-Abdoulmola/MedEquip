<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupplierSuggestionService
{
    /**
     * Suggest suppliers for an RFQ based on multiple factors.
     */
    public function suggestForRfq(Rfq $rfq, int $limit = 10): Collection
    {
        $rfq->load('items.product');

        // Get all verified and active suppliers
        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->get();

        // Calculate match score for each supplier
        $scoredSuppliers = $suppliers->map(function ($supplier) use ($rfq) {
            return [
                'supplier' => $supplier,
                'score' => $this->calculateMatchScore($supplier, $rfq),
                'breakdown' => $this->getScoreBreakdown($supplier, $rfq),
            ];
        });

        // Sort by score descending
        $sorted = $scoredSuppliers->sortByDesc('score');

        // Return top N suppliers
        return $sorted->take($limit)->values();
    }

    /**
     * Calculate match score for a supplier and RFQ (0-100).
     */
    public function calculateMatchScore(Supplier $supplier, Rfq $rfq): float
    {
        $weights = [
            'product_availability' => 0.40,  // 40%
            'past_performance' => 0.25,      // 25%
            'response_rate' => 0.20,         // 20%
            'proximity' => 0.15,              // 15%
        ];

        $scores = [
            'product_availability' => $this->scoreProductAvailability($supplier, $rfq),
            'past_performance' => $this->scorePastPerformance($supplier, $rfq->buyer_id),
            'response_rate' => $this->scoreResponseRate($supplier),
            'proximity' => $this->scoreProximity($supplier, $rfq),
        ];

        // Calculate weighted total
        $totalScore = 0;
        foreach ($scores as $key => $score) {
            $totalScore += $score * $weights[$key];
        }

        return round($totalScore, 2);
    }

    /**
     * Get detailed score breakdown.
     */
    public function getScoreBreakdown(Supplier $supplier, Rfq $rfq): array
    {
        return [
            'product_availability' => $this->scoreProductAvailability($supplier, $rfq),
            'past_performance' => $this->scorePastPerformance($supplier, $rfq->buyer_id),
            'response_rate' => $this->scoreResponseRate($supplier),
            'proximity' => $this->scoreProximity($supplier, $rfq),
        ];
    }

    /**
     * Score product availability (0-100).
     */
    private function scoreProductAvailability(Supplier $supplier, Rfq $rfq): float
    {
        $totalItems = $rfq->items->count();
        if ($totalItems === 0) {
            return 0;
        }

        $availableCount = 0;

        foreach ($rfq->items as $item) {
            if (!$item->product_id) {
                continue;
            }

            // Check if supplier has this product
            $hasProduct = DB::table('product_supplier')
                ->where('supplier_id', $supplier->id)
                ->where('product_id', $item->product_id)
                ->where('status', 'available')
                ->exists();

            if ($hasProduct) {
                $availableCount++;
            }
        }

        return ($availableCount / $totalItems) * 100;
    }

    /**
     * Score past performance with this buyer (0-100).
     */
    private function scorePastPerformance(Supplier $supplier, int $buyerId): float
    {
        // Check if supplier has worked with this buyer before
        $pastOrders = Order::where('buyer_id', $buyerId)
            ->where('supplier_id', $supplier->id)
            ->get();

        if ($pastOrders->isEmpty()) {
            return 50; // Neutral score if no history
        }

        $score = 50; // Base score

        // Positive factors
        $completedOrders = $pastOrders->where('status', Order::STATUS_DELIVERED)->count();
        if ($completedOrders > 0) {
            $score += min(30, $completedOrders * 5); // Up to +30 for completed orders
        }

        // Negative factors
        $cancelledOrders = $pastOrders->where('status', Order::STATUS_CANCELLED)->count();
        if ($cancelledOrders > 0) {
            $score -= min(20, $cancelledOrders * 10); // Up to -20 for cancellations
        }

        return max(0, min(100, $score));
    }

    /**
     * Score response rate to RFQs (0-100).
     */
    private function scoreResponseRate(Supplier $supplier): float
    {
        // Get RFQs where this supplier was invited
        $totalRfqs = DB::table('rfq_supplier')
            ->where('supplier_id', $supplier->id)
            ->count();

        if ($totalRfqs === 0) {
            return 50; // Neutral if no RFQ history
        }

        // Count quotations submitted
        $quotationsCount = Quotation::where('supplier_id', $supplier->id)->count();

        $responseRate = ($quotationsCount / $totalRfqs) * 100;

        return min(100, $responseRate);
    }

    /**
     * Score geographic proximity (0-100).
     */
    private function scoreProximity(Supplier $supplier, Rfq $rfq): float
    {
        $buyer = $rfq->buyer;

        if (!$buyer || !$supplier->city || !$buyer->city) {
            return 50; // Neutral if location data missing
        }

        // Same city = 100
        if (strtolower($supplier->city) === strtolower($buyer->city)) {
            return 100;
        }

        // Different city but same country = 70
        // (Assuming all in Libya for now)
        return 70;
    }

    /**
     * Get supplier recommendation reasons.
     */
    public function getRecommendationReasons(Supplier $supplier, Rfq $rfq): array
    {
        $reasons = [];
        $breakdown = $this->getScoreBreakdown($supplier, $rfq);

        if ($breakdown['product_availability'] >= 80) {
            $reasons[] = 'يوفر معظم المنتجات المطلوبة';
        }

        if ($breakdown['past_performance'] >= 70) {
            $reasons[] = 'سجل أداء ممتاز مع طلباتك السابقة';
        }

        if ($breakdown['response_rate'] >= 80) {
            $reasons[] = 'معدل استجابة عالي لطلبات العروض';
        }

        if ($breakdown['proximity'] >= 90) {
            $reasons[] = 'موجود في نفس المدينة (توصيل أسرع)';
        }

        if (empty($reasons)) {
            $reasons[] = 'مورد موثوق ومعتمد';
        }

        return $reasons;
    }
}
