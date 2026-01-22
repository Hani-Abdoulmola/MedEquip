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
        // Main cart table - one cart per buyer (can have multiple saved carts later)
        Schema::create('buyer_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->cascadeOnDelete()
                ->comment('FK -> buyers.id');
            $table->string('name')->nullable()->comment('Cart name for saved carts');
            $table->boolean('is_active')->default(true)->comment('Active cart flag');
            $table->boolean('is_saved')->default(false)->comment('Is this a saved cart template?');
            $table->timestamp('expires_at')->nullable()->comment('Cart expiration (30 days for active carts)');
            $table->timestamps();
            
            // Indexes
            $table->index(['buyer_id', 'is_active']);
            $table->index('expires_at');
        });

        // Cart items table
        Schema::create('buyer_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('buyer_carts')
                ->cascadeOnDelete()
                ->comment('FK -> buyer_carts.id');
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete()
                ->comment('FK -> products.id');
            $table->integer('quantity')->default(1);
            $table->text('specifications')->nullable()->comment('Custom specifications for this item');
            $table->string('unit', 50)->default('وحدة')->comment('Unit of measurement');
            $table->foreignId('supplier_id')->nullable()
                ->constrained('suppliers')
                ->nullOnDelete()
                ->comment('Preferred supplier for this item');
            $table->timestamps();
            
            // Indexes
            $table->index(['cart_id', 'product_id']);
            $table->unique(['cart_id', 'product_id', 'supplier_id'], 'unique_cart_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_cart_items');
        Schema::dropIfExists('buyer_carts');
    }
};
