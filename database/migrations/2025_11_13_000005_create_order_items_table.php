<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('quotation_item_id')->nullable()->constrained('quotation_items')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            // Item details
            $table->string('item_name', 200);
            $table->text('specifications')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit', 50)->nullable();
            
            // Pricing
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            
            // Item-specific details
            $table->string('lead_time', 100)->nullable();
            $table->string('warranty', 100)->nullable();
            
            // Item status (allows partial fulfillment)
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_production',
                'ready_to_ship',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending');
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['order_id', 'product_id']);
            $table->index(['order_id', 'status']);
            $table->index(['quotation_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

