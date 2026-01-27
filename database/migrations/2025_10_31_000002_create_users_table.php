<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي للمستخدم');

            $table->foreignId('user_type_id')
                ->nullable()
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->nullOnDelete()
                ->comment('🔗 FK -> user_types.id نوع المستخدم (Admin / Supplier / Buyer)');

            $table->string('name', 150)->comment('👤 اسم المستخدم الكامل');
            $table->string('email', 150)->unique()->comment('📧 البريد الإلكتروني (أساسي للمصادقة)');
            $table->string('phone', 30)->nullable()->index()->comment('📞 رقم الهاتف (اختياري)');
            $table->timestamp('email_verified_at')->nullable()->comment('⏱️ وقت تحقق البريد الإلكتروني');
            $table->string('password')->comment('🔐 كلمة المرور المشفّرة');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->index()->comment('⚙️ حالة الحساب');
            $table->rememberToken()->comment('🔑 رمز تذكّر الجلسة (login token)');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('👨‍💻 المستخدم الذي أنشأ الحساب');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('✏️ آخر من عدّل الحساب');
            $table->timestamps();
            $table->softDeletes()->comment('🗑️ حذف منطقي دون فقد البيانات');
        });
    }

    public function down(): void
    {
        // Drop foreign key constraints that reference users table first
        // Check if sent_notifications table exists and drop its foreign key
        if (Schema::hasTable('sent_notifications')) {
            Schema::table('sent_notifications', function (Blueprint $table) {
                $table->dropForeign(['sender_id']);
            });
        }
        
        // Drop self-referencing foreign keys in users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
            });
        }
        
        // Drop the users table
        Schema::dropIfExists('users');
    }
};
