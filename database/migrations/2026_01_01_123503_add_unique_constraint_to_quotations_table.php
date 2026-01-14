<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add unique constraint to prevent duplicate quotations from same supplier for same RFQ.
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add unique constraint on (rfq_id, supplier_id) to prevent duplicates
            $table->unique(['rfq_id', 'supplier_id'], 'rfq_supplier_quotation_unique');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropUnique('rfq_supplier_quotation_unique');
        });
    }
};
