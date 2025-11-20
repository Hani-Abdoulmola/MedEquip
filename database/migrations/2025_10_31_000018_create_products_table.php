<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول المنتجات (Products)
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للمنتج');

            //  المستخدمين الذين أنشأوا أو عدّلوا المنتج
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment(' المستخدم الذي أضاف المنتج (عادة مدير النظام أو المورد)');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('✏️ آخر من قام بتعديل بيانات المنتج');

            //  البيانات الأساسية للمنتج
            $table->string('name', 200)->comment('📦 اسم المنتج الطبي مثل: جهاز تخدير، مضخة حقن، إلخ');
            $table->string('model', 100)->nullable()->comment('🔢 رقم أو موديل المنتج');
            $table->string('brand', 100)->nullable()->comment('🏷️ العلامة التجارية للمنتج');

            // 📂 الفئة (علاقة مع جدول product_categories)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete()
                ->comment('📂 فئة المنتج (علاقة مع جدول الفئات الهرمي)');

            $table->text('description')->nullable()->comment('📝 وصف تفصيلي للمنتج ومواصفاته التقنية');

            //  حالة المنتج العامة (متاح أو غير متاح للعرض)
            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment(' حالة المنتج: متاح / غير متاح للعرض');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('🗑️ الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين البحث والأداء
            $table->index(['name', 'brand'], 'product_search_index');
            $table->index(['category_id', 'is_active'], 'product_category_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
