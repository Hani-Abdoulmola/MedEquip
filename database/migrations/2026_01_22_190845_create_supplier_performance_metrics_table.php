<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('period_start')->comment('Metric period start date');
            $table->date('period_end')->comment('Metric period end date');
            
            // Core metrics
            $table->decimal('overall_score', 5, 2)->default(0)->comment('Overall performance score 0-100');
            $table->decimal('average_rating', 3, 2)->default(0)->comment('Average review rating 1-5');
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('completed_orders')->default(0);
            $table->integer('cancelled_orders')->default(0);
            
            // Financial metrics
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            
            // Performance metrics
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0)->comment('Percentage 0-100');
            $table->decimal('response_rate', 5, 2)->default(0)->comment('RFQ response rate 0-100');
            $table->decimal('fulfillment_rate', 5, 2)->default(0)->comment('Order fulfillment rate 0-100');
            $table->decimal('average_response_time_hours', 8, 2)->default(0);
            $table->decimal('average_delivery_days', 6, 2)->default(0);
            
            // Quality metrics
            $table->integer('quality_issues_count')->default(0);
            $table->integer('returns_count')->default(0);
            $table->integer('disputes_count')->default(0);
            
            // Engagement metrics
            $table->integer('rfqs_received')->default(0);
            $table->integer('quotations_submitted')->default(0);
            $table->integer('orders_won')->default(0);
            $table->decimal('win_rate', 5, 2)->default(0)->comment('Percentage 0-100');
            
            // Badges earned (JSON array)
            $table->json('badges')->nullable()->comment('Earned performance badges');
            
            // Rankings
            $table->integer('category_rank')->nullable()->comment('Rank in supplier category');
            $table->integer('overall_rank')->nullable()->comment('Overall marketplace rank');
            
            $table->timestamps();
            
            // Indexes
            $table->index('supplier_id');
            $table->index(['period_start', 'period_end']);
            $table->index('overall_score');
            $table->unique(['supplier_id', 'period_start', 'period_end'], 'supplier_perf_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_performance_metrics');
    }
};
