<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول المدفوعات (Payments)
     * لتتبع كل العمليات المالية بين المشتري والمورد
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي لعملية الدفع');

            // 🔹 العلاقات الخارجية
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->restrictOnDelete()
                ->comment('🧾 FK -> invoices.id (الفاتورة المرتبطة)');

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->restrictOnDelete()
                ->comment('📦 FK -> orders.id (الطلب المرتبط إن وجد)');

            $table->foreignId('buyer_id')
                ->nullable()
                ->constrained('buyers')
                ->nullOnDelete()
                ->comment('👤 المشتري الذي قام بالدفع');

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete()
                ->comment('🏢 المورد المستفيد من الدفع');

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('⚙️ المستخدم الذي نفّذ أو أكد الدفع');

            // 💰 البيانات المالية
            $table->string('payment_reference', 100)
                ->unique()
                ->nullable()
                ->comment('🔗 معرّف مرجعي داخلي لعملية الدفع');

            $table->decimal('amount', 12, 2)
                ->comment('💵 قيمة المبلغ المدفوع بدقة مالية عالية');

            $table->string('currency', 10)
                ->default('LYD')
                ->comment('💱 العملة المستخدمة في الدفع (الدينار الليبي افتراضياً)');

            $table->enum('method', [
                'cash', 'bank_transfer', 'credit_card', 'paypal', 'other',
            ])->default('cash')->comment('💳 طريقة الدفع (نقدي، تحويل بنكي، بطاقة، بايبال...)');

            $table->enum('payment_type', [
                'advance', 'final', 'refund',
            ])->nullable()->comment('📘 نوع الدفع: دفعة مقدمة / نهائية / استرجاع');

            $table->string('transaction_id')
                ->nullable()
                ->comment('🏦 رقم العملية البنكية أو المرجع المالي الخارجي');

            $table->enum('status', [
                'pending', 'completed', 'failed', 'refunded',
            ])->default('pending')->comment('📊 حالة الدفع (قيد الانتظار / مكتمل / فشل / تم الاسترجاع)');

            $table->text('notes')->nullable()->comment('📝 ملاحظات إضافية حول عملية الدفع');
            $table->timestamp('paid_at')->nullable()->comment('📅 تاريخ تنفيذ الدفع');

            $table->timestamps();
            $table->softDeletes()->comment('🗑️ الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء
            $table->index(['status', 'paid_at'], 'payment_status_index');
            $table->index(['buyer_id', 'supplier_id', 'status'], 'payment_party_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
