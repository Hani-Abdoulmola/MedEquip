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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignId('rfq_item_id')->nullable()->constrained('rfq_items')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            // Item details
            $table->string('item_name', 200);
            $table->text('specifications')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit', 50)->nullable();
            
            // Pricing
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            
            // Supplier-specific details
            $table->string('lead_time', 100)->nullable();
            $table->string('warranty', 100)->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['quotation_id', 'rfq_item_id']);
            $table->index(['quotation_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};

