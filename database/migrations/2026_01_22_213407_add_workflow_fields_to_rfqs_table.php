<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds workflow tracking fields to rfqs table:
     * - published_at: When RFQ was published (draft → open)
     * - awarded_at: When RFQ was awarded to a quotation
     * - cancelled_at: When RFQ was cancelled
     * - awarded_quotation_id: FK to the winning quotation
     */
    public function up(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            // Workflow timestamps
            $table->timestamp('published_at')->nullable()->after('deadline')
                ->comment('تاريخ نشر الطلب (من مسودة إلى مفتوح)');
            
            $table->timestamp('awarded_at')->nullable()->after('closed_at')
                ->comment('تاريخ ترسية الطلب على مورد');
            
            $table->timestamp('cancelled_at')->nullable()->after('awarded_at')
                ->comment('تاريخ إلغاء الطلب');
            
            // Reference to winning quotation
            $table->foreignId('awarded_quotation_id')->nullable()
                ->after('cancelled_at')
                ->constrained('quotations')
                ->nullOnDelete()
                ->comment('FK -> quotations.id عرض السعر الفائز');
            
            // Index for workflow queries
            $table->index(['status', 'deadline', 'published_at'], 'rfq_workflow_status_deadline_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropForeign(['awarded_quotation_id']);
            $table->dropIndex('rfq_workflow_status_deadline_idx');
            $table->dropColumn([
                'published_at',
                'awarded_at',
                'cancelled_at',
                'awarded_quotation_id',
            ]);
        });
    }
};
