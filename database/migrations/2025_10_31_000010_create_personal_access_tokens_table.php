<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول personal_access_tokens (Sanctum) لتخزين التوكنات الشخصية / API tokens
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id()->comment('PK: معرف التوكن');
            $table->morphs('tokenable'); // tokenable_id, tokenable_type -> الكيان الذي أنشأ التوكن (عادة User)
            $table->string('name')->comment('اسم التوكن أو وصفه');
            $table->string('token', 64)->unique()->comment('قيمة التوكن (مخزّن بشكل آمن)');
            $table->text('abilities')->nullable()->comment('القدرات الممنوحة للتوكن (json/text)');
            $table->timestamp('last_used_at')->nullable()->comment('آخر استخدام للتوكن');
            $table->timestamp('expires_at')->nullable()->index()->comment('تاريخ انتهاء صلاحية التوكن');
            $table->timestamps(); // created_at, updated_at

            // مؤشرات مفيدة
            // $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
