<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول لتتبع المهام الفاشلة
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id()->comment('PK: معرف سجل الفشل');
            $table->string('uuid')->unique()->comment('UUID فريد لسجل الفشل');
            $table->text('connection')->comment('اسم الاتصال/driver المستخدم');
            $table->text('queue')->comment('اسم الطابور');
            $table->longText('payload')->comment('البيانات المرسلة للمهمة');
            $table->longText('exception')->comment('نص الاستثناء أو الخطأ');
            $table->timestamp('failed_at')->useCurrent()->comment('وقت الفشل');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
