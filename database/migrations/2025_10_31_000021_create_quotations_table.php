<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول عروض الأسعار (Quotations)
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id()->comment('المعرّف الأساسي لعرض السعر');

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete()
                ->comment('FK -> rfqs.id يشير إلى طلب عرض السعر الذي تم الرد عليه');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->comment('FK -> suppliers.id يشير إلى المورد الذي قدّم العرض');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('👤 المستخدم الذي أنشأ عرض السعر');

            $table->string('reference_code', 100)->unique()->comment('كود مرجعي فريد لتعقّب عرض السعر');
            $table->decimal('total_price', 12, 2)->comment('السعر الإجمالي المقترح للعرض (دقة مالية عالية)');
            $table->text('terms')->nullable()->comment('شروط الدفع والتسليم الخاصة بالعرض');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->comment('حالة العرض: قيد الانتظار / مقبول / مرفوض');
            $table->text('notes')->nullable()->comment('ملاحظات داخلية أو تعليقات على العرض');
            $table->timestamp('valid_until')->nullable()->comment('تاريخ انتهاء صلاحية العرض');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء
            $table->index(['rfq_id', 'supplier_id', 'status'], 'quotation_management_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
