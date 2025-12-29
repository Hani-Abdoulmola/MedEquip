<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول الربط بين المنتجات والموردين (Product ↔ Supplier)
     */
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id()->comment(' المعرّف الأساسي للسجل');

            //  العلاقات الخارجية
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment(' FK → المنتجات المرتبطة بالمورد');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment(' FK → المورد الذي يقدّم المنتج');

            //  معلومات التسعير والمخزون
            $table->decimal('price', 10, 2)
                ->default(0)
                ->comment('💵 السعر الذي يقدمه المورد للمنتج');

            $table->unsignedInteger('stock_quantity')
                ->default(0)
                ->comment('📦 الكمية المتوفرة لدى المورد من هذا المنتج');

            //  وقت التوريد والتوصيل
            $table->string('lead_time', 100)
                ->nullable()
                ->comment(' مدة التوصيل أو التسليم المتوقعة (مثلاً: 3 أيام - أسبوع)');

            //  حالة عرض المنتج من المورد
            $table->enum('status', ['available', 'out_of_stock', 'suspended'])
                ->default('available')
                ->comment('✅ حالة توفر المنتج لدى المورد');

            //  بيانات إضافية اختيارية
            $table->string('warranty', 100)
                ->nullable()
                ->comment('🛠️ مدة الضمان أو الشروط الخاصة للمورد');

            $table->text('notes')
                ->nullable()
                ->comment('🧾 ملاحظات إضافية من المورد حول المنتج');

            //  البيانات الزمنية
            $table->timestamps();

            //  فهارس الأداء والقيود
            $table->unique(['product_id', 'supplier_id'], 'unique_product_supplier');
            $table->index(['status', 'supplier_id'], 'status_supplier_index');
            $table->index(['price', 'product_id'], 'price_product_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
