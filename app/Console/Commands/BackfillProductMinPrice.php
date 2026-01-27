<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillProductMinPrice extends Command
{
    protected $signature = 'products:backfill-min-price';
    protected $description = 'Backfill min_price and suppliers_count for all products (Phase 1)';

    public function handle(): int
    {
        if (!\Schema::hasColumn('products', 'min_price')) {
            $this->warn('products.min_price column missing. Run migrations first.');
            return self::FAILURE;
        }

        $products = Product::pluck('id');
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $productId) {
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
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
        return self::SUCCESS;
    }
}
