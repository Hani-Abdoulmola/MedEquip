<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            // Dispute details
            $table->string('dispute_number')->unique()->comment('Unique dispute reference');
            $table->enum('type', [
                'not_delivered',
                'late_delivery',
                'damaged_products',
                'wrong_products',
                'missing_items',
                'quality_issue',
                'other'
            ]);
            $table->string('title');
            $table->text('description');
            
            // Evidence
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            
            // Resolution
            $table->enum('status', [
                'open',
                'investigating',
                'waiting_supplier',
                'waiting_buyer',
                'resolved',
                'closed',
                'escalated'
            ])->default('open');
            
            $table->enum('resolution', [
                'refund',
                'replacement',
                'partial_refund',
                'redelivery',
                'no_action',
                'other'
            ])->nullable();
            
            $table->text('resolution_details')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            
            // Timeline
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Communication log (JSON)
            $table->json('messages')->nullable()->comment('Dispute conversation history');
            
            // Admin notes
            $table->text('admin_notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('delivery_id');
            $table->index('buyer_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->index('type');
            $table->index('dispute_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_disputes');
    }
};
