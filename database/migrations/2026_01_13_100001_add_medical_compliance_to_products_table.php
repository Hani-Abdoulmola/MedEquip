<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add SKU, Medical Compliance, and Catalog Control Fields to Products Table
 * 
 * This migration enhances the products table to support:
 * - Unique product identification (SKU)
 * - Medical device classification (FDA, CE, ISO)
 * - Canonical catalog control (versioning, source tracking, duplicate detection)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ==========================================
            // Unique Product Identification
            // ==========================================
            $table->string('sku', 50)
                ->nullable()
                ->unique()
                ->after('id')
                ->comment('Stock Keeping Unit - unique product identifier');

            // ==========================================
            // Medical Device Classification
            // ==========================================
            $table->enum('medical_class', ['I', 'IIa', 'IIb', 'III', 'exempt', 'not_applicable'])
                ->nullable()
                ->after('certifications')
                ->comment('FDA/Medical device classification');

            $table->boolean('ce_marked')
                ->default(false)
                ->after('medical_class')
                ->comment('CE marking for European compliance');

            $table->boolean('fda_cleared')
                ->default(false)
                ->after('ce_marked')
                ->comment('FDA 510(k) or PMA cleared');

            $table->string('iso_certification', 100)
                ->nullable()
                ->after('fda_cleared')
                ->comment('ISO certification number (e.g., ISO 13485)');

            // ==========================================
            // Catalog Control & Versioning
            // ==========================================
            $table->unsignedInteger('version')
                ->default(1)
                ->after('iso_certification')
                ->comment('Product data version for audit trail');

            $table->enum('source', ['admin', 'supplier_request', 'import', 'seeder'])
                ->default('supplier_request')
                ->after('version')
                ->comment('Origin of the product entry');

            $table->string('canonical_hash', 64)
                ->nullable()
                ->after('source')
                ->comment('Hash of name+brand+model for duplicate detection');

            // ==========================================
            // Indexes
            // ==========================================
            $table->index('canonical_hash', 'product_canonical_hash_index');
            $table->index('medical_class', 'product_medical_class_index');
            $table->index('source', 'product_source_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('product_canonical_hash_index');
            $table->dropIndex('product_medical_class_index');
            $table->dropIndex('product_source_index');
            
            // Drop columns
            $table->dropColumn([
                'sku',
                'medical_class',
                'ce_marked',
                'fda_cleared',
                'iso_certification',
                'version',
                'source',
                'canonical_hash',
            ]);
        });
    }
};

