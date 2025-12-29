<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للمصنّع');

            $table->string('name', 150)
                ->comment('اسم الشركة المصنّعة – مثل: Apple, Siemens, Philips');

            $table->string('name_ar', 150)
                ->nullable()
                ->comment('الاسم بالعربية إن وجد');

            $table->string('slug')
                ->unique()
                ->comment('معرّف URL فريد للمصنِّع');

            // اختياري: ربطه بفئة معينة (بعض المنصات تخلي المصنِّع مرتبط بأكثر من فئة، الآن نخلوه بسيط)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete()
                ->comment('فئة المنتجات الرئيسية لهذا المصنّع (اختياري)');

            $table->string('country', 100)->nullable()->comment('بلد المصنّع');
            $table->string('website', 200)->nullable()->comment('الموقع الرسمي للمصنّع');

            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('هل المصنّع نشط في الكتالوج؟');

            $table->timestamps();
            $table->softDeletes()->comment('🗑️ الحذف المنطقي');

            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
