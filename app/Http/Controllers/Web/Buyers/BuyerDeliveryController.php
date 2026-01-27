<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Services\NotificationService;
use App\Services\OrderDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Buyer Delivery Controller
 *
 * Handles delivery tracking for buyers.
 * Buyers can view their deliveries and confirm receipt.
 * 
 * Note: Buyer verification is handled by the 'buyer.verified' middleware.
 */
class BuyerDeliveryController extends Controller
{
    /**
     * Display list of deliveries for the buyer.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Delivery::class);

        $buyer = Auth::user()->buyerProfile;

        $query = Delivery::with(['order.supplier.user', 'order.items'])
            ->whereHas('order', function ($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Date range filter with quick filters
        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            match ($dateFilter) {
                'today' => $query->whereDate('delivery_date', today()),
                'this_week' => $query->whereBetween('delivery_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('delivery_date', now()->month)->whereYear('delivery_date', now()->year),
                'overdue' => $query->where('delivery_date', '<', now())->whereNotIn('status', ['delivered', 'failed']),
                default => null,
            };
        } else {
            // Custom date range
            if ($request->filled('from_date')) {
                $query->whereDate('delivery_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('delivery_date', '<=', $request->to_date);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('supplier', fn($s) => $s->where('company_name', 'like', "%{$search}%"));
                  });
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15)->withQueryString();

        // Stats calculation
        $stats = Delivery::whereHas('order', function ($q) use ($buyer) {
            $q->where('buyer_id', $buyer->id);
        })
        ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_transit,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN delivery_date < NOW() AND status NOT IN (?, ?) THEN 1 ELSE 0 END) as overdue
        ', [
            Delivery::STATUS_PENDING,
            Delivery::STATUS_IN_TRANSIT,
            Delivery::STATUS_DELIVERED,
            Delivery::STATUS_FAILED,
            Delivery::STATUS_DELIVERED,
            Delivery::STATUS_FAILED,
        ])
        ->first();

        $stats = [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'in_transit' => $stats->in_transit ?? 0,
            'delivered' => $stats->delivered ?? 0,
            'failed' => $stats->failed ?? 0,
            'overdue' => $stats->overdue ?? 0,
        ];

        // Log activity
        activity('buyer_deliveries')
            ->causedBy(Auth::user())
            ->withProperties([
                'buyer_id' => $buyer->id,
                'filters' => $request->only(['status', 'from_date', 'to_date', 'search']),
            ])
            ->log('عرض المشتري قائمة التوصيلات');

        return view('buyer.deliveries.index', compact('deliveries', 'stats'));
    }

    /**
     * Display delivery details.
     */
    public function show(Delivery $delivery): View
    {
        $this->authorize('view', $delivery);

        $buyer = Auth::user()->buyerProfile;

        $delivery->load(['order.supplier.user', 'order.items.product', 'media']);

        // Log activity
        activity('buyer_deliveries')
            ->performedOn($delivery)
            ->causedBy(Auth::user())
            ->withProperties([
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
            ])
            ->log('عرض المشتري تفاصيل التوصيل: ' . ($delivery->delivery_number ?? $delivery->id));

        return view('buyer.deliveries.show', compact('delivery'));
    }

    /**
     * Confirm delivery receipt.
     */
    public function confirmReceipt(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorize('confirmReceipt', $delivery);

        $buyer = Auth::user()->buyerProfile;

        // Only in_transit or pending deliveries can be confirmed
        if (!in_array($delivery->status, [Delivery::STATUS_IN_TRANSIT, Delivery::STATUS_PENDING])) {
            return back()->withErrors(['error' => 'لا يمكن تأكيد استلام هذه الشحنة']);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $delivery->update([
                'status' => Delivery::STATUS_DELIVERED,
                'delivery_date' => now(),
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'receiver_name' => Auth::user()->name,
                'notes' => $request->notes ?? $delivery->notes,
            ]);

            // Update order status to delivered if all deliveries are delivered
            $order = $delivery->order;
            $allDelivered = $order->deliveries()->where('status', '!=', Delivery::STATUS_DELIVERED)->count() === 0;
            
            if ($allDelivered) {
                $order->update(['status' => 'delivered']);
                
                // Auto-create delivery and invoice if not exists
                $orderDeliveryService = app(OrderDeliveryService::class);
                $orderDeliveryService->handleOrderDelivered($order, Auth::user());
            }

            // Notify supplier
            if ($order->supplier && $order->supplier->user) {
                NotificationService::send(
                    $order->supplier->user,
                    '✅ تم تأكيد استلام الشحنة',
                    "قام المشتري {$buyer->organization_name} بتأكيد استلام الشحنة للطلب رقم {$order->order_number}",
                    route('supplier.deliveries.show', $delivery->id),
                    'fas fa-check-circle',
                    'success'
                );
            }

            // Log activity
            activity('buyer_deliveries')
                ->performedOn($delivery)
                ->causedBy(Auth::user())
                ->withProperties([
                    'delivery_id' => $delivery->id,
                    'order_id' => $order->id,
                    'action' => 'confirm_receipt',
                ])
                ->log('قام المشتري بتأكيد استلام الشحنة');

            DB::commit();

            return redirect()
                ->route('buyer.deliveries.show', $delivery)
                ->with('success', 'تم تأكيد استلام الشحنة بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer confirm delivery error', [
                'delivery_id' => $delivery->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تأكيد الاستلام']);
        }
    }

    /**
     * Get status label in Arabic.
     */
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            Delivery::STATUS_PENDING => 'قيد الانتظار',
            Delivery::STATUS_IN_TRANSIT => 'في الطريق',
            Delivery::STATUS_DELIVERED => 'تم التسليم',
            Delivery::STATUS_FAILED => 'فشل التسليم',
            default => $status,
        };
    }

    /**
     * Get status color for UI.
     */
    public static function getStatusColor(string $status): string
    {
        return match($status) {
            Delivery::STATUS_PENDING => 'yellow',
            Delivery::STATUS_IN_TRANSIT => 'blue',
            Delivery::STATUS_DELIVERED => 'green',
            Delivery::STATUS_FAILED => 'red',
            default => 'gray',
        };
    }
}

