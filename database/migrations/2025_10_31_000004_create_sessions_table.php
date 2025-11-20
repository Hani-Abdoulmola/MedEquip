<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول sessions عند استخدام session driver = database
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('معرف الجلسة (PK)');
            $table->foreignId('user_id')->nullable()->index()->comment('FK -> users.id (قد يكون nullable للـ guest)');
            $table->string('ip_address', 45)->nullable()->comment('عنوان IP المرتبط بالجلسة');
            $table->text('user_agent')->nullable()->comment('معلومات User-Agent للمتصفح/العميل');
            $table->longText('payload')->comment('بيانات الجلسة المسلسلة');
            $table->integer('last_activity')->index()->comment('وقت آخر نشاط (unix timestamp)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
