<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create pivot table for RFQ-Supplier assignments
     */
    public function up(): void
    {
        Schema::create('rfq_supplier', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete()
                ->comment('RFQ المرتبط');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->comment('المورد المعين');

            $table->enum('status', ['invited', 'viewed', 'quoted', 'declined'])
                ->default('invited')
                ->comment('حالة المورد في هذا الطلب');

            $table->timestamp('invited_at')->nullable()->comment('تاريخ الدعوة');
            $table->timestamp('viewed_at')->nullable()->comment('تاريخ المشاهدة');
            $table->text('notes')->nullable()->comment('ملاحظات');

            $table->timestamps();

            $table->unique(['rfq_id', 'supplier_id'], 'rfq_supplier_unique');
            $table->index(['supplier_id', 'status'], 'supplier_rfq_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_supplier');
    }
};
