<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create buyer_favorites pivot table for product favorites
     */
    public function up(): void
    {
        Schema::create('buyer_favorites', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->cascadeOnDelete()
                ->comment('FK -> buyers.id المشتري الذي أضاف المنتج للمفضلة');
            
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->comment('FK -> products.id المنتج المضاف للمفضلة');
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicate favorites
            $table->unique(['buyer_id', 'product_id'], 'buyer_product_favorite_unique');
            
            // Index for quick lookup
            $table->index(['buyer_id', 'created_at'], 'buyer_favorites_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_favorites');
    }
};

