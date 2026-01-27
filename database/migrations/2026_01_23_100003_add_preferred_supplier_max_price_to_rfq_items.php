<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1: Preserve preferred supplier and max price when creating RFQ from builder.
     */
    public function up(): void
    {
        Schema::table('rfq_items', function (Blueprint $table) {
            $table->foreignId('preferred_supplier_id')->nullable()->after('unit')
                ->constrained('suppliers')->nullOnDelete()
                ->comment('Preferred supplier from RFQ builder');
            $table->decimal('max_price', 12, 2)->nullable()->after('preferred_supplier_id')
                ->comment('Buyer budget for this item');
        });
    }

    public function down(): void
    {
        Schema::table('rfq_items', function (Blueprint $table) {
            $table->dropForeign(['preferred_supplier_id']);
            $table->dropColumn(['preferred_supplier_id', 'max_price']);
        });
    }
};
