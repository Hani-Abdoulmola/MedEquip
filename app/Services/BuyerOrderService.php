<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Buyer Order Service (Phase 1)
 *
 * Handles order listing, details, stats, and reorder flows.
 */
class BuyerOrderService
{
    public function __construct(
        protected RfqBuilderService $builderService,
        protected ReferenceCodeService $referenceService
    ) {}

    /**
     * Get orders for buyer with filters and pagination.
     */
    public function getOrders(Buyer $buyer, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::query()
            ->with(['supplier.user', 'items', 'quotation.rfq'])
            ->where('buyer_id', $buyer->id);

        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                    ->orWhere('notes', 'like', "%{$s}%")
                    ->orWhereHas('supplier', fn($sub) => $sub->where('company_name', 'like', "%{$s}%"))
                    ->orWhereHas('quotation', fn($sub) => $sub->where('reference_code', 'like', "%{$s}%"));
            });
        }

        if (!empty($filters['date_filter'])) {
            match ($filters['date_filter']) {
                'today' => $query->whereDate('order_date', today()),
                'this_week' => $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year),
                'last_month' => $query->whereMonth('order_date', now()->subMonth()->month)->whereYear('order_date', now()->subMonth()->year),
                default => null,
            };
        } else {
            if (!empty($filters['date_from'])) {
                $query->whereDate('order_date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('order_date', '<=', $filters['date_to']);
            }
        }

        return $query->latest('order_date')->paginate($perPage)->withQueryString();
    }

    /**
     * Get order details with relations.
     */
    public function getOrderDetails(int $orderId, Buyer $buyer): Order
    {
        $order = Order::query()
            ->with([
                'supplier.user',
                'items.product',
                'quotation.rfq',
                'quotation.items',
                'invoices',
                'deliveries',
            ])
            ->where('buyer_id', $buyer->id)
            ->findOrFail($orderId);

        return $order;
    }

    /**
     * Get order stats for buyer (single query).
     *
     * @return array{total: int, pending: int, processing: int, shipped: int, delivered: int, cancelled: int, total_spending: float}
     */
    public function getOrderStats(Buyer $buyer): array
    {
        $row = Order::query()
            ->where('buyer_id', $buyer->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled,
                COALESCE(SUM(total_amount), 0) as total_spending
            ', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ])
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'pending' => (int) ($row->pending ?? 0),
            'processing' => (int) ($row->processing ?? 0),
            'shipped' => (int) ($row->shipped ?? 0),
            'delivered' => (int) ($row->delivered ?? 0),
            'cancelled' => (int) ($row->cancelled ?? 0),
            'total_spending' => (float) ($row->total_spending ?? 0),
        ];
    }

    /**
     * Reorder: add order items to RFQ builder. Returns [added, skipped, skipped_items, message].
     *
     * @return array{added: int, skipped: int, skipped_items: array, message: string}
     */
    public function reorderToBuilder(Order $order, Buyer $buyer): array
    {
        if ($order->buyer_id !== $buyer->id) {
            throw new \InvalidArgumentException(__('ليس لديك صلاحية لإعادة طلب هذا الأمر'));
        }

        $builder = $this->builderService->getOrCreateBuilder($buyer);
        $order->load('items.product');

        $added = 0;
        $skipped = 0;
        $skippedItems = [];

        foreach ($order->items as $item) {
            $reason = null;
            if (!$item->product) {
                $reason = 'المنتج غير موجود';
            } elseif (!$item->product->is_active) {
                $reason = 'المنتج غير نشط';
            } elseif ($item->product->review_status !== 'approved') {
                $reason = 'المنتج غير معتمد';
            }
            
            if ($reason) {
                $skipped++;
                $skippedItems[] = [
                    'name' => $item->item_name ?? 'منتج غير معروف',
                    'reason' => $reason,
                ];
                continue;
            }
            
            try {
                $this->builderService->addProduct($builder, $item->product, [
                    'quantity' => $item->quantity,
                    'specifications' => $item->specifications,
                    'unit' => $item->unit ?? 'وحدة',
                    'supplier_id' => $order->supplier_id,
                ]);
                $added++;
            } catch (\Throwable $e) {
                $skipped++;
                $skippedItems[] = [
                    'name' => $item->product->name ?? $item->item_name,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $message = "تم إضافة {$added} منتج إلى منشئ الطلبات";
        if ($skipped > 0) {
            $message .= " ({$skipped} منتج غير متوفر تم تخطيه)";
        }

        return ['added' => $added, 'skipped' => $skipped, 'skipped_items' => $skippedItems, 'message' => $message];
    }

    /**
     * Reorder: create new RFQ from order (draft). Returns [rfq, added, skipped, message].
     *
     * @return array{rfq: \App\Models\Rfq, added: int, skipped: int, message: string}
     */
    public function reorderToRfq(Order $order, Buyer $buyer): array
    {
        if ($order->buyer_id !== $buyer->id) {
            throw new \InvalidArgumentException(__('ليس لديك صلاحية لإعادة طلب هذا الأمر'));
        }

        $order->load(['items.product', 'quotation.rfq']);

        DB::beginTransaction();
        try {
            $rfq = \App\Models\Rfq::create([
                'buyer_id' => $buyer->id,
                'created_by' => auth()->id(),
                'title' => "إعادة طلب: {$order->order_number}",
                'description' => $order->quotation?->rfq?->description ?? "إعادة طلب من الطلب رقم {$order->order_number}",
                'deadline' => now()->addDays(7),
                'is_public' => $order->quotation?->rfq?->is_public ?? true,
                'status' => 'draft',
                'reference_code' => $this->referenceService->generateUnique(ReferenceCodeService::PREFIX_RFQ, \App\Models\Rfq::class),
            ]);

            $added = 0;
            $skipped = 0;

            foreach ($order->items as $item) {
                if (!$item->product || !$item->product->is_active || $item->product->review_status !== 'approved') {
                    $skipped++;
                    continue;
                }
                \App\Models\RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name ?? $item->product->name,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit ?? 'وحدة',
                    'preferred_supplier_id' => $order->supplier_id,
                ]);
                $added++;
            }

            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(auth()->user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'reference_code' => $rfq->reference_code,
                    'original_order_id' => $order->id,
                    'original_order_number' => $order->order_number,
                    'items_added' => $added,
                    'items_skipped' => $skipped,
                ])
                ->log('قام المشتري بإنشاء RFQ من طلب سابق (إعادة طلب)');

            DB::commit();

            $message = 'تم إنشاء طلب عرض سعر جديد بنجاح';
            if ($skipped > 0) {
                $message .= " ({$skipped} منتج غير متوفر تم تخطيه)";
            }

            return ['rfq' => $rfq, 'added' => $added, 'skipped' => $skipped, 'message' => $message];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BuyerOrderService::reorderToRfq failed', [
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public static function getStatusLabel(string $status): string
    {
        return match ($status) {
            Order::STATUS_PENDING => 'قيد الانتظار',
            Order::STATUS_PROCESSING => 'قيد المعالجة',
            Order::STATUS_SHIPPED => 'تم الشحن',
            Order::STATUS_DELIVERED => 'تم التسليم',
            Order::STATUS_CANCELLED => 'ملغى',
            default => $status,
        };
    }

    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            Order::STATUS_PENDING => 'yellow',
            Order::STATUS_PROCESSING => 'blue',
            Order::STATUS_SHIPPED => 'indigo',
            Order::STATUS_DELIVERED => 'green',
            Order::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }
}
