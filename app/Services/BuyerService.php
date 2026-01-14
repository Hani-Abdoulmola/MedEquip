<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\BuyerFavorite;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Rfq;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

/**
 * BuyerService
 * 
 * Handles business logic and data aggregation for the Buyer module.
 * Centralizes dashboard statistics, RFQ operations, and quotation management.
 */
class BuyerService
{
    /**
     * Get dashboard statistics for a buyer.
     * 
     * @param Buyer $buyer
     * @return array
     */
    public function getDashboardStats(Buyer $buyer): array
    {
        // Use a single query with selectRaw for better performance
        $rfqStats = Rfq::where('buyer_id', $buyer->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
                SUM(CASE WHEN status = 'awarded' THEN 1 ELSE 0 END) as awarded,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();

        // Quotation stats
        $quotationStats = Quotation::whereHas('rfq', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        // Order stats
        $orderStats = Order::where('buyer_id', $buyer->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                COALESCE(SUM(total_amount), 0) as total_spending
            ")
            ->first();

        // Favorites count
        $favoritesCount = BuyerFavorite::where('buyer_id', $buyer->id)->count();

        return [
            'rfqs' => [
                'total' => $rfqStats->total ?? 0,
                'draft' => $rfqStats->draft ?? 0,
                'open' => $rfqStats->open ?? 0,
                'under_review' => $rfqStats->under_review ?? 0,
                'closed' => $rfqStats->closed ?? 0,
                'awarded' => $rfqStats->awarded ?? 0,
                'cancelled' => $rfqStats->cancelled ?? 0,
            ],
            'quotations' => [
                'total' => $quotationStats->total ?? 0,
                'pending' => $quotationStats->pending ?? 0,
                'accepted' => $quotationStats->accepted ?? 0,
                'rejected' => $quotationStats->rejected ?? 0,
            ],
            'orders' => [
                'total' => $orderStats->total ?? 0,
                'pending' => $orderStats->pending ?? 0,
                'processing' => $orderStats->processing ?? 0,
                'shipped' => $orderStats->shipped ?? 0,
                'delivered' => $orderStats->delivered ?? 0,
                'cancelled' => $orderStats->cancelled ?? 0,
                'total_spending' => number_format($orderStats->total_spending ?? 0, 2) . ' د.ل',
            ],
            'favorites' => $favoritesCount,
        ];
    }

    /**
     * Get recent RFQs for a buyer.
     * 
     * @param Buyer $buyer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentRfqs(Buyer $buyer, int $limit = 5)
    {
        return Rfq::where('buyer_id', $buyer->id)
            ->with(['items', 'quotations'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent quotations for buyer's RFQs.
     * 
     * @param Buyer $buyer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentQuotations(Buyer $buyer, int $limit = 5)
    {
        return Quotation::whereHas('rfq', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
            ->with(['rfq', 'supplier.user'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activities for a buyer.
     * 
     * @param Buyer $buyer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities(Buyer $buyer, int $limit = 10)
    {
        return Activity::where('causer_id', $buyer->user_id)
            ->where('causer_type', 'App\\Models\\User')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending quotations that need buyer's review.
     * 
     * @param Buyer $buyer
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingQuotationsForReview(Buyer $buyer)
    {
        return Quotation::whereHas('rfq', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id)
              ->whereIn('status', ['open', 'under_review']);
        })
            ->where('status', 'pending')
            ->with(['rfq', 'supplier.user', 'items'])
            ->latest()
            ->get();
    }

    /**
     * Get spending trend for the last N days.
     * 
     * @param Buyer $buyer
     * @param int $days
     * @return array
     */
    public function getSpendingTrend(Buyer $buyer, int $days = 7): array
    {
        $data = Order::where('buyer_id', $buyer->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('total_amount')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill missing days with 0
        $result = [];
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result[] = floatval($data[$date] ?? 0);
            $labels[] = now()->subDays($i)->format('D'); // Day name
        }

        return [
            'data' => $result,
            'labels' => $labels,
        ];
    }

    /**
     * Get RFQ status distribution for charts.
     * 
     * @param Buyer $buyer
     * @return array
     */
    public function getRfqStatusDistribution(Buyer $buyer): array
    {
        $stats = Rfq::where('buyer_id', $buyer->id)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [
            'draft' => 'مسودة',
            'open' => 'مفتوح',
            'under_review' => 'قيد المراجعة',
            'closed' => 'مغلق',
            'awarded' => 'تم الترسية',
            'cancelled' => 'ملغي',
        ];

        $result = [];
        foreach ($labels as $key => $label) {
            $result[] = [
                'label' => $label,
                'value' => $stats[$key] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Check if buyer can create a new RFQ.
     * 
     * @param Buyer $buyer
     * @return bool
     */
    public function canCreateRfq(Buyer $buyer): bool
    {
        return $buyer->is_verified && $buyer->is_active;
    }

    /**
     * Check if buyer can accept a quotation.
     * 
     * @param Buyer $buyer
     * @param Quotation $quotation
     * @return bool
     */
    public function canAcceptQuotation(Buyer $buyer, Quotation $quotation): bool
    {
        // Check ownership
        if (!$quotation->rfq || $quotation->rfq->buyer_id !== $buyer->id) {
            return false;
        }

        // Check quotation status
        if ($quotation->status !== 'pending') {
            return false;
        }

        // Check RFQ status
        if (!in_array($quotation->rfq->status, ['open', 'under_review'])) {
            return false;
        }

        return true;
    }

    /**
     * Toggle product favorite status.
     * 
     * @param Buyer $buyer
     * @param int $productId
     * @return array ['added' => bool, 'count' => int]
     */
    public function toggleFavorite(Buyer $buyer, int $productId): array
    {
        $existing = BuyerFavorite::where('buyer_id', $buyer->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            BuyerFavorite::create([
                'buyer_id' => $buyer->id,
                'product_id' => $productId,
            ]);
            $added = true;
        }

        $count = BuyerFavorite::where('buyer_id', $buyer->id)->count();

        return [
            'added' => $added,
            'count' => $count,
        ];
    }

    /**
     * Get buyer's favorite products.
     * 
     * @param Buyer $buyer
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFavoriteProducts(Buyer $buyer, int $perPage = 15)
    {
        return BuyerFavorite::where('buyer_id', $buyer->id)
            ->with(['product' => function ($q) {
                $q->with(['category', 'manufacturer', 'media']);
            }])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Check if a product is in buyer's favorites.
     * 
     * @param Buyer $buyer
     * @param int $productId
     * @return bool
     */
    public function isFavorite(Buyer $buyer, int $productId): bool
    {
        return BuyerFavorite::where('buyer_id', $buyer->id)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get upcoming events for buyer's calendar.
     * 
     * @param Buyer $buyer
     * @return array
     */
    public function getUpcomingEvents(Buyer $buyer): array
    {
        $events = [];

        // RFQ deadlines
        $rfqDeadlines = Rfq::where('buyer_id', $buyer->id)
            ->whereIn('status', ['open', 'draft'])
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(30))
            ->get(['id', 'title', 'deadline']);

        foreach ($rfqDeadlines as $rfq) {
            $events[] = [
                'title' => 'موعد إغلاق: ' . $rfq->title,
                'date' => $rfq->deadline->format('Y-m-d'),
                'color' => 'bg-medical-blue-500',
                'type' => 'rfq_deadline',
            ];
        }

        // Pending quotation reviews (created in last 7 days)
        $pendingQuotes = Quotation::whereHas('rfq', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(7))
            ->with('rfq')
            ->get();

        foreach ($pendingQuotes as $quote) {
            $events[] = [
                'title' => 'مراجعة عرض: ' . ($quote->rfq->title ?? 'غير معروف'),
                'date' => $quote->created_at->format('Y-m-d'),
                'color' => 'bg-medical-green-500',
                'type' => 'quotation_review',
            ];
        }

        // Sort by date
        usort($events, fn($a, $b) => $a['date'] <=> $b['date']);

        return $events;
    }
}

