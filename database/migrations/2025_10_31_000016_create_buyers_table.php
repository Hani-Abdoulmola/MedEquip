<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول المشترين (Buyers)
     */
    public function up(): void
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id()->comment('المعرّف الأساسي للمشتري');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('FK -> users.id يشير إلى المستخدم المرتبط بالمشتري (مستشفى/عيادة/مختبر)');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('👤 المستخدم الإداري الذي أنشأ السجل');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('✏️ آخر من قام بتعديل بيانات المشتري');

            $table->string('organization_name', 200)->comment('اسم المنشأة مثل: مستشفى السلام أو مختبر النور');
            $table->string('organization_type', 100)->nullable()->comment('نوع المنشأة: مستشفى / عيادة / مختبر / مركز طبي');
            $table->string('license_number', 100)->nullable()->comment('رقم الترخيص الصحي للمنشأة');
            // ✅ مستندات الترخيص تُدار عبر Spatie Media Library (media table)
            // لا حاجة لحقل license_document - استخدم $buyer->addMedia() بدلاً من ذلك
            $table->string('country', 100)->nullable()->comment('بلد المشتري');
            $table->string('city', 100)->nullable()->comment('المدينة التي تقع فيها المنشأة');
            $table->string('address', 255)->nullable()->comment('العنوان الكامل للمشتري');
            $table->string('contact_email', 150)->nullable()->comment('بريد إلكتروني خاص بالتواصل التجاري');
            $table->string('contact_phone', 50)->nullable()->comment('رقم الهاتف التجاري الرئيسي');

            $table->boolean('is_verified')->default(false)->index()->comment('هل تم التحقق من المشتري من قِبل إدارة المنصة؟');
            $table->timestamp('verified_at')->nullable()->comment('تاريخ اعتماد المشتري بعد المراجعة');
            $table->boolean('is_active')->default(true)->index()->comment('هل الحساب نشط ويمكن للمشتري الدخول؟');
            $table->text('rejection_reason')->nullable()->comment('سبب رفض طلب التسجيل (إن وُجد)');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء في عمليات البحث والإدارة
            $table->index(['organization_name', 'country', 'city', 'is_verified'], 'buyer_search_index');
            $table->index(['user_id', 'is_verified']);
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
