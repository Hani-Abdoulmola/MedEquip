<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfq_items', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للبند داخل الطلب');

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete()
                ->comment('🔗 FK → rfqs.id (يربط البند بالطلب الرئيسي)');

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete()
                ->comment('🔗 FK → products.id (إن كان المنتج مسجلاً في النظام)');

            $table->string('item_name', 200)->comment('📦 اسم المنتج المطلوب (في حال لم يكن مرتبطًا بمنتج)');
            $table->text('specifications')->nullable()->comment('🧾 المواصفات الفنية أو التفاصيل الخاصة بالبند');
            $table->integer('quantity')->default(1)->comment('🔢 الكمية المطلوبة من هذا المنتج');
            $table->string('unit', 50)->nullable()->comment('📏 وحدة القياس مثل: قطعة / كرتونة / لتر');

            $table->boolean('is_approved')->default(false)->comment('✅ هل تمت الموافقة على هذا البند من قبل المشتري؟');
            $table->timestamp('approved_at')->nullable()->comment('⏱️ تاريخ الموافقة (اختياري)');

            $table->timestamps();
            $table->softDeletes()->comment('🗑️ حذف ناعم دون فقد البيانات');

            // فهارس للأداء
            $table->index(['rfq_id', 'product_id', 'is_approved'], 'rfq_item_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_items');
    }
};
