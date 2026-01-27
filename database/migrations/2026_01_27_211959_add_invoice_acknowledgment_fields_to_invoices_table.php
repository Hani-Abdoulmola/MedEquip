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
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('acknowledged_at')->nullable()->after('approved_by')
                ->comment('تاريخ اعتراف المشتري بالفاتورة');
            $table->foreignId('acknowledged_by')->nullable()->after('acknowledged_at')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('المستخدم (المشتري) الذي اعترف بالفاتورة');
            $table->timestamp('disputed_at')->nullable()->after('acknowledged_by')
                ->comment('تاريخ الاعتراض على الفاتورة');
            $table->text('dispute_reason')->nullable()->after('disputed_at')
                ->comment('سبب الاعتراض على الفاتورة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['acknowledged_by']);
            $table->dropColumn(['acknowledged_at', 'acknowledged_by', 'disputed_at', 'dispute_reason']);
        });
    }
};
