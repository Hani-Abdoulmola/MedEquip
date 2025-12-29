<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للمنتج');

            // المستخدمين
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('المستخدم الذي أضاف المنتج');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('✏️ آخر من عدل المنتج');

            $table->foreignId('manufacturer_id')
            ->nullable()
            ->constrained('manufacturers')
            ->nullOnDelete()
            ->comment('الشركة المصنّعة الحقيقية للمنتج');

            // البيانات الأساسية
            $table->string('name', 200)->comment('📦 اسم المنتج');
            $table->string('model', 100)->nullable()->comment('🔢 الموديل');
            $table->string('brand', 100)->nullable()->comment('🏷️ العلامة التجارية');

            // الفئة
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete()
                ->comment('📂 الفئة');

            $table->text('description')->nullable()->comment('📝 الوصف العام للمنتج');

            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('نشط / غير نشط');

            $table->enum('review_status', ['pending', 'approved', 'needs_update', 'rejected'])
                ->default('pending')
                ->comment('حالة المراجعة من الإدارة');

            $table->text('review_notes')
                ->nullable()
                ->comment('ملاحظات الإدارة للمورد عند طلب تعديل');

            $table->text('rejection_reason')
                ->nullable()
                ->comment('سبب الرفض عند تغيير الحالة إلى rejected');

            $table->json('specifications')
                ->nullable()
                ->comment('مواصفات المنتج (key/value)');

            $table->json('features')
                ->nullable()
                ->comment('مميزات المنتج (list of strings)');

            $table->json('technical_data')
                ->nullable()
                ->comment('بيانات تقنية إضافية');

            $table->json('certifications')
                ->nullable()
                ->comment('الشهادات والاعتمادات');

            $table->text('installation_requirements')
                ->nullable()
                ->comment('متطلبات التركيب والتشغيل');

            $table->timestamps();
            $table->softDeletes()->comment('🗑️ الحذف المنطقي');

            // فهارس
            $table->index(['name', 'brand'], 'product_search_index');
            $table->index(['category_id', 'is_active'], 'product_category_index');
            $table->index(['manufacturer_id', 'category_id'], 'product_manufacturer_category_index');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
