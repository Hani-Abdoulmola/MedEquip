<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            // Ratings (1-5)
            $table->integer('overall_rating')->comment('Overall delivery rating 1-5');
            $table->integer('timeliness_rating')->nullable()->comment('On-time delivery rating');
            $table->integer('condition_rating')->nullable()->comment('Product condition rating');
            $table->integer('packaging_rating')->nullable()->comment('Packaging quality rating');
            $table->integer('professionalism_rating')->nullable()->comment('Delivery personnel rating');
            
            // Review content
            $table->string('title', 200)->nullable();
            $table->text('review')->nullable();
            $table->text('issues')->nullable()->comment('Any issues encountered');
            
            // Status and verification
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_verified_delivery')->default(true)->comment('Verified through system');
            
            // Photos
            $table->json('photos')->nullable()->comment('Delivery photos uploaded by buyer');
            
            // Moderation
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('delivery_id');
            $table->index('buyer_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->unique('delivery_id'); // One review per delivery
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_reviews');
    }
};
