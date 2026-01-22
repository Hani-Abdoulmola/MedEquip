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
        Schema::create('rfq_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('rfq_templates')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name')->comment('Item name');
            $table->text('specifications')->nullable()->comment('Item specifications');
            $table->integer('quantity')->default(1)->comment('Default quantity');
            $table->string('unit', 50)->default('وحدة')->comment('Unit of measurement');
            $table->integer('sort_order')->default(0)->comment('Display order');
            $table->timestamps();

            $table->index('template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_template_items');
    }
};
