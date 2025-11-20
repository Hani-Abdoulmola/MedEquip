<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول فئات المنتجات (Product Categories)
     * نظام هرمي يدعم الفئات الرئيسية والفرعية
     */
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للفئة');

            // 🔖 معلومات التعريف الأساسية
            $table->string('name')->comment('📦 اسم الفئة بالإنجليزية (مثل: Medical Imaging)');
            $table->string('name_ar')->nullable()->comment('📦 اسم الفئة بالعربية (مثل: التصوير الطبي)');
            $table->string('slug')->unique()->comment('🔗 معرّف URL فريد (مثل: medical-imaging)');
            $table->text('description')->nullable()->comment('📝 وصف تفصيلي للفئة ومحتوياتها');

            // 🌳 الهيكل الهرمي (Parent-Child)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete()
                ->comment('🌳 الفئة الأب (null = فئة رئيسية، رقم = فئة فرعية)');

            // ⚙️ إعدادات العرض والحالة
            $table->boolean('is_active')
                ->default(true)
                ->comment('✅ حالة الفئة: نشطة / غير نشطة');

            $table->integer('sort_order')
                ->default(0)
                ->comment('🔢 ترتيب العرض (الأصغر يظهر أولاً)');

            // 👤 تتبع المستخدمين
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('👤 المستخدم الذي أنشأ الفئة');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('✏️ آخر من قام بتعديل الفئة');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('🗑️ الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء
            // Index for querying active categories under a parent, sorted by display order
            $table->index(['parent_id', 'is_active', 'sort_order'], 'category_hierarchy_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};

