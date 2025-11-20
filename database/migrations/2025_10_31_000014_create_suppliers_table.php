<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول الموردين (Suppliers)
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للمورد');

            //  العلاقة مع المستخدم (الحساب المرتبط بالمورد)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('FK → users.id يشير إلى المستخدم الذي يمثل المورد');

            //  تتبع الإنشاء والتعديل
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('👤 المستخدم الإداري الذي أنشأ سجل المورد');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('✏️ آخر من قام بتعديل بيانات المورد');

            //  البيانات الأساسية
            $table->string('company_name', 200)->comment('🏢 اسم الشركة أو المؤسسة التجارية للمورد');
            $table->string('commercial_register', 100)->nullable()->comment('📜 رقم السجل التجاري');
            $table->string('tax_number', 100)->nullable()->comment('💰 الرقم الضريبي (إن وجد)');

            // ✅ مستندات التحقق تُدار عبر Spatie Media Library (media table)
            // لا حاجة لحقل verification_file_path - استخدم $supplier->addMedia() بدلاً من ذلك

            //  معلومات الموقع والتواصل
            $table->string('country', 100)->nullable()->comment('🌍 بلد المورد');
            $table->string('city', 100)->nullable()->comment('🏙️ المدينة التي يقع فيها المورد');
            $table->string('address', 255)->nullable()->comment('📫 العنوان الكامل للمورد');
            $table->string('contact_email', 150)->nullable()->comment('📧 البريد الإلكتروني للتواصل التجاري');
            $table->string('contact_phone', 50)->nullable()->comment('📞 رقم الهاتف التجاري');

            //  حالة التحقق والمصادقة
            $table->boolean('is_verified')->default(false)->index()->comment('هل المورد موثّق من إدارة المنصة؟');
            $table->timestamp('verified_at')->nullable()->comment('تاريخ اعتماد المورد بعد المراجعة');
            $table->boolean('is_active')->default(true)->index()->comment('هل الحساب نشط ويمكن للمورد الدخول؟');
            $table->text('rejection_reason')->nullable()->comment('سبب رفض طلب التسجيل (إن وُجد)');

            //  التواريخ وحالة الحذف
            $table->timestamps();
            $table->softDeletes()->comment('🗑️ الحذف المنطقي دون فقد البيانات');

            //  فهارس الأداء والبحث
            $table->unique('company_name', 'unique_supplier_company_name');
            $table->index(['company_name', 'country', 'city'], 'supplier_search_index');
            $table->index(['user_id', 'is_verified'], 'supplier_status_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
