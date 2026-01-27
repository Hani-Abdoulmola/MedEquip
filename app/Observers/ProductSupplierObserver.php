<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductSupplier;
use App\Services\BuyerAlertService;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1: Keep product min_price and suppliers_count in sync when product_supplier changes.
 * Phase 3: Record price history for alerts.
 */
class ProductSupplierObserver
{
    public function saved(ProductSupplier $pivot): void
    {
        $this->refreshProductCounts($pivot->product_id);
        
        // Phase 3: Record price history
        if (\Schema::hasTable('product_price_history')) {
            app(BuyerAlertService::class)->recordPriceHistory($pivot);
        }
    }

    public function deleted(ProductSupplier $pivot): void
    {
        $this->refreshProductCounts($pivot->product_id);
    }

    private function refreshProductCounts(int $productId): void
    {
        if (!\Schema::hasColumn('products', 'min_price')) {
            return;
        }

        $row = DB::table('product_supplier')
            ->join('suppliers', 'suppliers.id', '=', 'product_supplier.supplier_id')
            ->where('product_supplier.product_id', $productId)
            ->where('product_supplier.status', 'available')
            ->where('suppliers.is_verified', true)
            ->where('suppliers.is_active', true)
            ->selectRaw('MIN(product_supplier.price) as min_price, COUNT(*) as suppliers_count')
            ->first();

        Product::withoutEvents(function () use ($productId, $row) {
            Product::where('id', $productId)->update([
                'min_price' => $row?->min_price,
                'suppliers_count' => (int) ($row?->suppliers_count ?? 0),
            ]);
        });
    }
}
