<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول أوامر الشراء (Orders)
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('المعرّف الأساسي لأمر الشراء');

            // 🔹 العلاقات الخارجية
            $table->foreignId('quotation_id')
                ->constrained('quotations')
                ->restrictOnDelete()
                ->comment('FK -> quotations.id يشير إلى عرض السعر المقبول الذي تم إنشاء الطلب بناءً عليه');

            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->restrictOnDelete()
                ->comment('FK -> buyers.id يشير إلى المشتري الذي أجرى الطلب');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete()
                ->comment('FK -> suppliers.id يشير إلى المورد الذي تم تأكيد الطلب معه');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('FK -> users.id يشير إلى المستخدم الذي أنشأ الطلب فعليًا');

            // 🔹 تفاصيل الطلب
            $table->string('order_number', 100)->unique()->comment('رقم فريد لتعريف أمر الشراء');
            $table->timestamp('order_date')->useCurrent()->comment('تاريخ إنشاء الطلب');
            $table->enum('status', [
                'pending', 'processing', 'shipped', 'delivered', 'cancelled',
            ])->default('pending')->comment('حالة الطلب: قيد الانتظار / جاري التنفيذ / تم الشحن / تم التسليم / ملغي');
            $table->decimal('total_amount', 12, 2)->comment('القيمة الإجمالية لأمر الشراء (دقة مالية عالية)');
            $table->string('currency', 10)->default('LYD')->comment('العملة المستخدمة في الطلب');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية حول الطلب');

            // 🔹 معلومات عامة
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء وإدارة الطلبات
            $table->index(['buyer_id', 'supplier_id', 'status'], 'order_management_index');
            $table->index('order_number', 'order_number_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
