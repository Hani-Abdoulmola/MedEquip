<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول السجلّات (Activity Log)
     * يعتمد على Spatie Activity Log مع تحسينات إضافية لتتبع كل الحركات داخل النظام
     */
    public function up(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->create(config('activitylog.table_name'), function (Blueprint $table) {
                $table->bigIncrements('id')->comment('🔑 المعرّف الأساسي للسجل');

                // 🔹 اسم سجل النشاط أو الوحدة
                $table->string('log_name', 100)
                    ->nullable()
                    ->index()
                    ->comment('📘 اسم السجل أو الوحدة مثل: users, orders, products, auth');

                // 🔹 وصف النشاط
                $table->text('description')
                    ->comment('📝 وصف النشاط المنفذ (مثلاً: تعديل حالة الطلب، حذف منتج...)');

                // 🔹 نوع الحدث (حدث قياسي مثل created/updated/deleted/custom)
                $table->string('event', 50)
                    ->nullable()
                    ->index()
                    ->comment('🎯 نوع الحدث: created / updated / deleted / login / approval / custom');

                // 🔹 العلاقة مع العنصر المتأثر (Polymorphic)
                $table->nullableMorphs('subject', 'subject'); // subject_type, subject_id

                // 🔹 العلاقة مع المنفّذ (المستخدم أو النظام)
                $table->nullableMorphs('causer', 'causer'); // causer_type, causer_id

                // 🔹 خصائص إضافية (مثل القيم القديمة والجديدة)
                $table->json('properties')
                    ->nullable()
                    ->comment('📦 خصائص إضافية أو تفاصيل العملية بصيغة JSON');

                // 🔹 UUID لتجميع عدة أنشطة ضمن عملية واحدة
                $table->uuid('batch_uuid')
                    ->nullable()
                    ->index()
                    ->comment('🧩 UUID لتجميع الأنشطة المترابطة (Batch Actions)');

                // 🔹 تحسين إضافي: تحديد الوحدة (Module) والعملية (Action)
                $table->string('module', 100)
                    ->nullable()
                    ->index()
                    ->comment('🏷️ الوحدة أو القسم: Users / Orders / Suppliers / Auth / Products');

                $table->string('action', 100)
                    ->nullable()
                    ->index()
                    ->comment('⚙️ نوع العملية داخل الوحدة مثل: login, approve, verify, print');

                // 🔹 بيانات الجهاز والمستخدم
                $table->string('ip_address', 45)
                    ->nullable()
                    ->comment('🌐 عنوان IP للمستخدم المنفّذ للعملية');

                $table->string('user_agent', 500)
                    ->nullable()
                    ->comment('🧠 معلومات الجهاز أو المتصفح المستخدم في العملية');

                $table->string('platform', 100)
                    ->nullable()
                    ->comment('💻 نوع النظام أو الجهاز المستخدم (Web / Mobile / API)');

                // 🔹 الطوابع الزمنية
                $table->timestamps(); // created_at, updated_at
                $table->softDeletes()->comment('🗑️ حذف ناعم للسجل دون فقد البيانات');

                // 🔍 فهارس لتحسين الأداء في الاستعلامات الكبيرة
                $table->index(['log_name', 'event', 'created_at'], 'activity_event_index');
                $table->index(['module', 'action'], 'activity_module_action_index');
                $table->index(['causer_id', 'subject_id'], 'activity_user_subject_index');
            });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->dropIfExists(config('activitylog.table_name'));
    }
};
