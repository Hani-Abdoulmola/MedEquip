<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول الفواتير (Invoices)
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id()->comment('المعرّف الأساسي للفاتورة');

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete()
                ->comment('FK -> orders.id يشير إلى أمر الشراء المرتبط بالفاتورة');

            $table->string('invoice_number', 100)->unique()->comment('رقم الفاتورة الفريد');
            $table->timestamp('invoice_date')->useCurrent()->comment('تاريخ إنشاء الفاتورة');

            $table->decimal('subtotal', 12, 2)->comment('المجموع قبل الضرائب والخصومات (دقة مالية عالية)');
            $table->decimal('tax', 12, 2)->default(0)->comment('قيمة الضريبة المضافة (إن وُجدت)');
            $table->decimal('discount', 12, 2)->default(0)->comment('قيمة الخصم (إن وُجد)');
            $table->decimal('total_amount', 12, 2)->comment('المجموع النهائي بعد الخصومات والضرائب');

            $table->enum('status', [
                'draft', 'issued', 'approved', 'cancelled',
            ])->default('issued')->comment('حالة الفاتورة: مسودة / صادرة / معتمدة / ملغاة');

            $table->enum('payment_status', [
                'unpaid', 'partial', 'paid',
            ])->default('unpaid')->comment('الحالة المالية الإجمالية بناءً على عمليات الدفع المرتبطة');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('FK -> users.id المستخدم الذي أنشأ الفاتورة');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('FK -> users.id المستخدم الذي اعتمد الفاتورة');

            $table->text('notes')->nullable()->comment('ملاحظات إضافية حول الفاتورة أو الدفع');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('حذف منطقي دون فقدان البيانات');

            // 🔍 فهارس لتحسين الأداء في التقارير والمحاسبة
            $table->index(['order_id', 'status', 'payment_status', 'invoice_date'], 'invoice_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
