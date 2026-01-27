<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds workflow tracking fields to quotations table:
     * - submitted_at: When quotation was submitted (draft → pending)
     * - accepted_at, rejected_at, expired_at, withdrawn_at, converted_at: State transition timestamps
     * - accepted_by, rejected_by: User who performed the action (audit trail)
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Workflow timestamps
            $table->timestamp('submitted_at')->nullable()->after('created_at')
                ->comment('تاريخ تقديم العرض (من مسودة إلى معلق)');
            
            $table->timestamp('accepted_at')->nullable()->after('status')
                ->comment('تاريخ قبول العرض');
            
            $table->timestamp('rejected_at')->nullable()->after('accepted_at')
                ->comment('تاريخ رفض العرض');
            
            $table->timestamp('expired_at')->nullable()->after('rejected_at')
                ->comment('تاريخ انتهاء صلاحية العرض');
            
            $table->timestamp('withdrawn_at')->nullable()->after('expired_at')
                ->comment('تاريخ سحب العرض من قبل المورد');
            
            $table->timestamp('converted_at')->nullable()->after('withdrawn_at')
                ->comment('تاريخ تحويل العرض إلى أمر شراء');
            
            // Audit trail: who accepted/rejected
            $table->foreignId('accepted_by')->nullable()
                ->after('converted_at')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('FK -> users.id المستخدم الذي قبل العرض');
            
            $table->foreignId('rejected_by')->nullable()
                ->after('rejection_reason')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('FK -> users.id المستخدم الذي رفض العرض');
            
            // Index for workflow queries
            $table->index(['rfq_id', 'status', 'submitted_at'], 'quotation_workflow_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['accepted_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropIndex('quotation_workflow_status_idx');
            $table->dropColumn([
                'submitted_at',
                'accepted_at',
                'rejected_at',
                'expired_at',
                'withdrawn_at',
                'converted_at',
                'accepted_by',
                'rejected_by',
            ]);
        });
    }
};
