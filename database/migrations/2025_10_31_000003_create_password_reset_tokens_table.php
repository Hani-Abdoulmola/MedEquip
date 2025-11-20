<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول رموز استعادة كلمة المرور (بنية مبسطة ومأخوذة من بعض الحزم الحديثة)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment('البريد الإلكتروني المرتبط برمز الاسترجاع (PK)');
            $table->string('token')->comment('توكن إعادة تعيين كلمة المرور');
            $table->timestamp('created_at')->nullable()->comment('وقت إنشاء التوكن');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
