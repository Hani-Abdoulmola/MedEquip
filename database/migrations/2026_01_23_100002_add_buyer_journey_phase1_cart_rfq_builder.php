<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1: Add RFQ Builder / template fields to buyer_carts and buyer_cart_items.
     */
    public function up(): void
    {
        Schema::table('buyer_carts', function (Blueprint $table) {
            $table->string('template_name')->nullable()->after('name')
                ->comment('Name for saved RFQ builder template');
            $table->boolean('is_template')->default(false)->after('template_name')
                ->comment('Is this a saved template?');
            $table->string('source', 50)->default('manual')->after('is_template')
                ->comment('manual, reorder, template');
            $table->foreignId('original_order_id')->nullable()->after('source')
                ->constrained('orders')->nullOnDelete()
                ->comment('If created from reorder');
        });

        Schema::table('buyer_cart_items', function (Blueprint $table) {
            $table->decimal('max_price', 12, 2)->nullable()->after('supplier_id')
                ->comment('Buyer budget / max price for this item');
        });
    }

    public function down(): void
    {
        Schema::table('buyer_carts', function (Blueprint $table) {
            $table->dropForeign(['original_order_id']);
            $table->dropColumn(['template_name', 'is_template', 'source', 'original_order_id']);
        });
        Schema::table('buyer_cart_items', function (Blueprint $table) {
            $table->dropColumn('max_price');
        });
    }
};
