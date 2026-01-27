<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\BuyerPriceAlert;
use App\Models\BuyerStockAlert;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\ProductSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Buyer Alert Service (Phase 3)
 *
 * Manages price and stock alerts for buyers.
 */
class BuyerAlertService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Set price alert for a product.
     */
    public function setPriceAlert(Buyer $buyer, Product $product, float $targetPrice): BuyerPriceAlert
    {
        return BuyerPriceAlert::updateOrCreate(
            [
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ],
            [
                'target_price' => $targetPrice,
                'is_active' => true,
                'triggered_at' => null,
            ]
        );
    }

    /**
     * Remove price alert.
     */
    public function removePriceAlert(Buyer $buyer, int $productId): bool
    {
        return BuyerPriceAlert::where('buyer_id', $buyer->id)
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Set stock alert for a product (optionally for specific supplier).
     */
    public function setStockAlert(Buyer $buyer, Product $product, ?int $supplierId = null): BuyerStockAlert
    {
        return BuyerStockAlert::updateOrCreate(
            [
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
                'supplier_id' => $supplierId,
            ],
            [
                'is_active' => true,
                'triggered_at' => null,
            ]
        );
    }

    /**
     * Remove stock alert.
     */
    public function removeStockAlert(Buyer $buyer, int $productId, ?int $supplierId = null): bool
    {
        $query = BuyerStockAlert::where('buyer_id', $buyer->id)
            ->where('product_id', $productId);
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        return $query->delete();
    }

    /**
     * Check and trigger price alerts (called by scheduled command).
     */
    public function checkPriceAlerts(): int
    {
        $alerts = BuyerPriceAlert::where('is_active', true)
            ->whereNull('triggered_at')
            ->with(['buyer.user', 'product'])
            ->get();

        $triggered = 0;
        foreach ($alerts as $alert) {
            $minPrice = $alert->product->min_price;
            if ($minPrice && $minPrice <= $alert->target_price) {
                $this->triggerPriceAlert($alert);
                $triggered++;
            }
        }
        return $triggered;
    }

    /**
     * Check and trigger stock alerts (called by scheduled command).
     */
    public function checkStockAlerts(): int
    {
        $alerts = BuyerStockAlert::where('is_active', true)
            ->whereNull('triggered_at')
            ->with(['buyer.user', 'product', 'supplier'])
            ->get();

        $triggered = 0;
        foreach ($alerts as $alert) {
            $hasStock = $alert->product->suppliers()
                ->where('product_supplier.status', 'available')
                ->where('product_supplier.stock_quantity', '>', 0)
                ->when($alert->supplier_id, fn($q) => $q->where('suppliers.id', $alert->supplier_id))
                ->exists();

            if ($hasStock) {
                $this->triggerStockAlert($alert);
                $triggered++;
            }
        }
        return $triggered;
    }

    /**
     * Record price history snapshot (called when product_supplier changes).
     */
    public function recordPriceHistory(ProductSupplier $pivot): void
    {
        ProductPriceHistory::create([
            'product_id' => $pivot->product_id,
            'supplier_id' => $pivot->supplier_id,
            'price' => $pivot->price,
            'stock_quantity' => $pivot->stock_quantity,
            'recorded_at' => now(),
        ]);
    }

    private function triggerPriceAlert(BuyerPriceAlert $alert): void
    {
        $alert->update(['triggered_at' => now(), 'is_active' => false]);
        
        if ($alert->buyer->user) {
            $this->notificationService->send(
                $alert->buyer->user,
                '💰 تنبيه انخفاض السعر',
                "انخفض سعر المنتج '{$alert->product->name}' إلى {$alert->product->min_price} د.ل (هدفك: {$alert->target_price} د.ل)",
                route('buyer.products.show', $alert->product)
            );
        }
    }

    private function triggerStockAlert(BuyerStockAlert $alert): void
    {
        $alert->update(['triggered_at' => now(), 'is_active' => false]);
        
        if ($alert->buyer->user) {
            $supplierText = $alert->supplier ? " من {$alert->supplier->company_name}" : '';
            $this->notificationService->send(
                $alert->buyer->user,
                '📦 تنبيه توفر المنتج',
                "المنتج '{$alert->product->name}' متوفر الآن{$supplierText}",
                route('buyer.products.show', $alert->product)
            );
        }
    }
}
