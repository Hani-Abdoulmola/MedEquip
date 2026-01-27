<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1: Add denormalized min_price & suppliers_count to products for performance.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('min_price', 12, 2)->nullable()->after('review_status')
                ->comment('Min price across active suppliers (denormalized)');
            $table->unsignedInteger('suppliers_count')->default(0)->after('min_price')
                ->comment('Count of active suppliers (denormalized)');
        });

        // Composite index for product_supplier filters/sorting
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->index(['product_id', 'status', 'price'], 'product_supplier_product_status_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->dropIndex('product_supplier_product_status_price');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['min_price', 'suppliers_count']);
        });
    }
};
