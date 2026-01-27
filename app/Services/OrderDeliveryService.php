<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Order Delivery Service
 * 
 * Handles automatic creation of Delivery and Invoice records
 * when an order status changes to 'delivered'.
 */
class OrderDeliveryService
{
    /**
     * Handle order delivered status - create delivery and invoice if needed
     * 
     * @param Order $order
     * @param User $triggeredBy
     * @return array{delivery: ?Delivery, invoice: ?Invoice}
     */
    public function handleOrderDelivered(Order $order, User $triggeredBy): array
    {
        $delivery = null;
        $invoice = null;

        // Create delivery if needed
        try {
            $delivery = $this->createDeliveryIfNeeded($order, $triggeredBy);
        } catch (\Throwable $e) {
            Log::error('Auto delivery creation failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            NotificationService::notifyAdmins(
                '⚠️ فشل إنشاء التسليم تلقائياً',
                "فشل إنشاء التسليم تلقائياً للطلب رقم {$order->order_number}. يرجى إنشاؤه يدوياً.",
                route('supplier.orders.show', $order->id)
            );
        }

        // Create invoice if needed (only if delivery was created or already exists)
        try {
            $invoice = $this->createInvoiceIfNeeded($order, $triggeredBy);
        } catch (\Throwable $e) {
            Log::error('Auto invoice creation failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            NotificationService::notifyAdmins(
                '⚠️ فشل إنشاء الفاتورة تلقائياً',
                "فشل إنشاء الفاتورة تلقائياً للطلب رقم {$order->order_number}. يرجى إنشاؤها يدوياً.",
                route('supplier.orders.show', $order->id)
            );
        }

        return [
            'delivery' => $delivery,
            'invoice' => $invoice,
        ];
    }

    /**
     * Create delivery record if it doesn't exist
     * 
     * @param Order $order
     * @param User $createdBy
     * @return Delivery|null
     */
    private function createDeliveryIfNeeded(Order $order, User $createdBy): ?Delivery
    {
        // Check if delivery already exists for this order
        $existingDelivery = Delivery::where('order_id', $order->id)
            ->where('status', Delivery::STATUS_DELIVERED)
            ->first();

        if ($existingDelivery) {
            return $existingDelivery;
        }

        // Load buyer relationship if not already loaded
        $order->load('buyer');

        // Prepare delivery data with fallback values
        $deliveryData = [
            'order_id' => $order->id,
            'supplier_id' => $order->supplier_id,
            'buyer_id' => $order->buyer_id,
            'created_by' => $createdBy->id,
            'delivery_number' => ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_DELIVERY,
                Delivery::class,
                'delivery_number'
            ),
            'delivery_date' => now(),
            'status' => Delivery::STATUS_DELIVERED,
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $createdBy->id,
            'delivery_location' => $order->buyer?->address ?? $order->notes ?? 'سيتم تحديده لاحقاً',
            'receiver_name' => $order->buyer?->user?->name ?? $order->buyer?->organization_name ?? 'غير محدد',
            'receiver_phone' => $order->buyer?->contact_phone ?? 'غير محدد',
            'notes' => 'تم التسليم تلقائياً عند تحديث حالة الطلب إلى "تم التسليم"',
        ];

        // Create new delivery record
        $delivery = Delivery::create($deliveryData);

        // Log delivery creation
        activity('deliveries')
            ->performedOn($delivery)
            ->causedBy($createdBy)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'auto_created' => true,
                'delivery_number' => $delivery->delivery_number,
            ])
            ->log('🚚 تم إنشاء تسليم تلقائياً عند تحديث حالة الطلب');

        // 🔔 Notify about delivery creation
        NotificationService::notifyAdmins(
            '🚚 تسليم جديد',
            "تم إنشاء تسليم رقم {$delivery->delivery_number} تلقائياً للطلب رقم {$order->order_number}.",
            route('admin.deliveries.show', $delivery->id)
        );

        // Notify buyer about delivery
        if ($order->buyer && $order->buyer->user) {
            NotificationService::send(
                $order->buyer->user,
                '🚚 تم تسليم طلبك',
                "تم تسليم طلبك رقم {$order->order_number} بنجاح. يمكنك الآن مراجعة الفاتورة.",
                route('buyer.orders.show', $order->id)
            );
        }

        return $delivery;
    }

    /**
     * Create invoice record if it doesn't exist
     * 
     * @param Order $order
     * @param User $createdBy
     * @return Invoice|null
     */
    private function createInvoiceIfNeeded(Order $order, User $createdBy): ?Invoice
    {
        // Check if invoice already exists
        $existingInvoice = Invoice::where('order_id', $order->id)->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Load order items if not already loaded
        $order->load('items');

        // Calculate invoice amounts
        // Priority: Use order->total_amount as source of truth if available
        // Otherwise calculate from order items
        if ($order->total_amount && $order->total_amount > 0) {
            // Use order total_amount as the source of truth
            $totalAmount = $order->total_amount;

            // Calculate tax and discount from items if available
            if ($order->items->isNotEmpty()) {
                $tax = $order->items->sum('tax_amount') ?? 0;
                $discount = $order->items->sum('discount_amount') ?? 0;
                $subtotal = $totalAmount - $tax + $discount;
            } else {
                // No items, assume no tax or discount
                $subtotal = $totalAmount;
                $tax = 0;
                $discount = 0;
            }
        } elseif ($order->items->isNotEmpty()) {
            // Calculate from order items
            $subtotal = $order->items->sum(function ($item) {
                return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
            });
            $tax = $order->items->sum('tax_amount') ?? 0;
            $discount = $order->items->sum('discount_amount') ?? 0;
            $totalAmount = $subtotal + $tax - $discount;
        } else {
            // Fallback: no items and no total_amount
            $subtotal = 0;
            $tax = 0;
            $discount = 0;
            $totalAmount = 0;
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_INVOICE,
                Invoice::class,
                'invoice_number'
            ),
            'invoice_date' => now(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'status' => Invoice::STATUS_ISSUED,
            'payment_status' => Invoice::PAYMENT_UNPAID,
            'created_by' => $createdBy->id,
            'notes' => "فاتورة تلقائية للطلب رقم {$order->order_number}",
        ]);

        // Log invoice creation
        activity('invoices')
            ->performedOn($invoice)
            ->causedBy($createdBy)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'auto_created' => true,
                'total_amount' => $totalAmount,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
            ])
            ->log('🧾 تم إنشاء فاتورة تلقائياً بعد التسليم');

        // 🔔 Notify about invoice creation
        NotificationService::notifyAdmins(
            '🧾 فاتورة جديدة',
            "تم إنشاء فاتورة رقم {$invoice->invoice_number} تلقائياً للطلب رقم {$order->order_number} بقيمة {$totalAmount}.",
            route('admin.invoices.show', $invoice->id)
        );

        // Notify buyer about invoice
        if ($order->buyer && $order->buyer->user) {
            NotificationService::send(
                $order->buyer->user,
                '📄 فاتورة جديدة لطلبك',
                "تم إصدار فاتورة جديدة للطلب رقم {$order->order_number} بقيمة {$totalAmount} د.ل. يرجى مراجعة الفاتورة والدفع.",
                route('buyer.orders.show', $order->id)
            );
        }

        // Notify supplier about invoice
        if ($order->supplier && $order->supplier->user) {
            NotificationService::send(
                $order->supplier->user,
                '💰 فاتورة جديدة',
                "تم إنشاء فاتورة رقم {$invoice->invoice_number} تلقائياً للطلب رقم {$order->order_number} بقيمة {$totalAmount} د.ل.",
                route('supplier.orders.show', $order->id)
            );
        }

        return $invoice;
    }
}
