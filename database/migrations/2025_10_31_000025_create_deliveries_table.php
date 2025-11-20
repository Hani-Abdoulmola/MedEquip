<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول عمليات التسليم (Deliveries)
     * لتوثيق وتتبع جميع عمليات تسليم الطلبات بين الموردين والمشترين
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي لعملية التسليم');

            // 🔹 العلاقات الخارجية
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete()
                ->comment('🔗 FK → orders.id يربط عملية التسليم بأمر الشراء');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete()
                ->comment('🏭 FK → suppliers.id يحدد المورد الذي يقوم بالتسليم');

            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->restrictOnDelete()
                ->comment('🏥 FK → buyers.id يحدد الجهة المستلمة (المشتري)');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('👤 المستخدم الذي أنشأ سجل عملية التسليم');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('✅ المستخدم الذي قام بتأكيد التسليم');

            // 🔹 تفاصيل التسليم
            $table->string('delivery_number', 100)->unique()->comment('📦 رقم التوصيل الفريد داخل النظام');
            $table->timestamp('delivery_date')->nullable()->comment('🕓 تاريخ التسليم الفعلي');
            $table->timestamp('verified_at')->nullable()->comment('⏱️ وقت تأكيد عملية التسليم فعليًا');
            $table->enum('status', [
                'pending', 'in_transit', 'delivered', 'failed',
            ])->default('pending')->comment('🚚 حالة التسليم: قيد الانتظار / في الطريق / تم التسليم / فشل');
            $table->string('delivery_location', 255)->nullable()->comment('📍 موقع التسليم الفعلي (إحداثيات أو عنوان)');
            $table->string('receiver_name', 150)->nullable()->comment('👤 اسم الشخص المستلم');
            $table->string('receiver_phone', 30)->nullable()->comment('📞 رقم هاتف المستلم');
            $table->boolean('is_verified')->default(false)->comment('🔒 هل تم تأكيد عملية التسليم من قبل المستلم؟');
            $table->text('notes')->nullable()->comment('🗒️ ملاحظات إضافية حول التسليم');

            // 🔹 الطوابع الزمنية
            $table->timestamps();
            $table->softDeletes()->comment('🗑️ حذف منطقي دون فقدان البيانات');

            // 🔍 فهارس للأداء العالي
            $table->index(['order_id', 'status', 'delivery_date'], 'delivery_index');
            $table->index(['supplier_id', 'buyer_id', 'status'], 'delivery_status_index');
            $table->index(['order_id', 'supplier_id', 'buyer_id', 'status'], 'delivery_composite_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
