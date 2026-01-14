<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Product Requests Table
 * 
 * Suppliers submit product requests which are reviewed by admin.
 * This enforces the canonical catalog model where:
 * - Suppliers CANNOT create products directly
 * - Suppliers SUBMIT requests that admin reviews
 * - Admin can approve (create new), merge (link to existing), or reject
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_requests', function (Blueprint $table) {
            $table->id()->comment('Primary key');

            // ==========================================
            // Request Source
            // ==========================================
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->comment('Supplier who submitted the request');

            $table->foreignId('existing_product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete()
                ->comment('If admin merges with existing product');

            // ==========================================
            // Product Data (Submitted by Supplier)
            // ==========================================
            $table->string('name', 200)->comment('Proposed product name');
            $table->string('model', 100)->nullable()->comment('Product model');
            $table->string('brand', 100)->nullable()->comment('Brand name');
            
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete()
                ->comment('Proposed category');

            $table->foreignId('manufacturer_id')
                ->nullable()
                ->constrained('manufacturers')
                ->nullOnDelete()
                ->comment('Proposed manufacturer');

            $table->text('description')->nullable()->comment('Product description');

            // JSON fields for structured data
            $table->json('specifications')->nullable()->comment('Technical specifications');
            $table->json('features')->nullable()->comment('Product features');
            $table->json('certifications')->nullable()->comment('Claimed certifications');
            $table->json('technical_data')->nullable()->comment('Technical data sheet info');
            $table->text('installation_requirements')->nullable();

            // Supplier's proposed offer data
            $table->decimal('proposed_price', 10, 2)->nullable()->comment('Supplier proposed price');
            $table->unsignedInteger('proposed_stock')->default(0)->comment('Proposed stock quantity');
            $table->string('proposed_lead_time', 100)->nullable();
            $table->string('proposed_warranty', 100)->nullable();

            // ==========================================
            // Request Workflow
            // ==========================================
            $table->enum('status', [
                'pending',      // Awaiting admin review
                'approved',     // Approved - new product created
                'merged',       // Merged with existing product
                'rejected',     // Rejected by admin
                'duplicate',    // Marked as duplicate (auto-detected)
                'cancelled'     // Cancelled by supplier
            ])->default('pending')->comment('Request status');

            $table->text('admin_notes')->nullable()->comment('Admin feedback/notes');
            $table->text('rejection_reason')->nullable()->comment('Reason if rejected');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin who reviewed');

            $table->timestamp('reviewed_at')->nullable()->comment('When reviewed');

            // ==========================================
            // Duplicate Detection
            // ==========================================
            $table->foreignId('duplicate_of')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete()
                ->comment('If marked as duplicate of existing product');

            $table->string('canonical_hash', 64)
                ->nullable()
                ->comment('Hash for duplicate detection');

            $table->decimal('similarity_score', 5, 2)
                ->nullable()
                ->comment('Similarity score to duplicate_of product (0-100)');

            // ==========================================
            // Timestamps
            // ==========================================
            $table->timestamps();
            $table->softDeletes();

            // ==========================================
            // Indexes
            // ==========================================
            $table->index(['supplier_id', 'status'], 'request_supplier_status_index');
            $table->index('status', 'request_status_index');
            $table->index('canonical_hash', 'request_canonical_hash_index');
            $table->index('reviewed_at', 'request_reviewed_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_requests');
    }
};

