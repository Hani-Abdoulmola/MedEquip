<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            
            // Ratings (1-5)
            $table->integer('overall_rating')->comment('Overall product rating 1-5');
            $table->integer('quality_rating')->nullable()->comment('Quality rating');
            $table->integer('value_rating')->nullable()->comment('Value for money rating');
            $table->integer('accuracy_rating')->nullable()->comment('Matches description rating');
            
            // Review content
            $table->string('title', 200)->nullable();
            $table->text('review');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->boolean('would_recommend')->default(true);
            
            // Status and verification
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_verified_purchase')->default(false);
            
            // Photos
            $table->json('photos')->nullable()->comment('Product photos uploaded by buyer');
            
            // Moderation
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Engagement
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('product_id');
            $table->index('buyer_id');
            $table->index('status');
            $table->index('overall_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
