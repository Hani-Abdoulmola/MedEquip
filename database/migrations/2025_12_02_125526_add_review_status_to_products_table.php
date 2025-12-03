<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add review_status column if it doesn't exist
            if (!Schema::hasColumn('products', 'review_status')) {
                $table->enum('review_status', ['pending', 'approved', 'needs_update', 'rejected'])
                    ->default('pending')
                    ->after('is_active')
                    ->comment('حالة المراجعة من الإدارة');
            }

            // Add review_notes column if it doesn't exist
            if (!Schema::hasColumn('products', 'review_notes')) {
                $table->text('review_notes')
                    ->nullable()
                    ->after('review_status')
                    ->comment('ملاحظات الإدارة للمورد عند طلب تعديل');
            }

            // Add rejection_reason column if it doesn't exist
            if (!Schema::hasColumn('products', 'rejection_reason')) {
                $table->text('rejection_reason')
                    ->nullable()
                    ->after('review_notes')
                    ->comment('سبب الرفض عند تغيير الحالة إلى rejected');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('products', 'review_notes')) {
                $table->dropColumn('review_notes');
            }
            if (Schema::hasColumn('products', 'review_status')) {
                $table->dropColumn('review_status');
            }
        });
    }
};
