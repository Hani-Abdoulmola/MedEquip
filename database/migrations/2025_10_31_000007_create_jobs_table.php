<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الطابور المؤقّت (queue driver = database)
        Schema::create('jobs', function (Blueprint $table) {
            $table->id()->comment('PK: معرف المهمة');
            $table->string('queue')->index()->comment('اسم طابور المهمة');
            $table->longText('payload')->comment('البيانات المسلسلة للمهمة');
            $table->unsignedTinyInteger('attempts')->comment('عدد المحاولات السابقة');
            $table->unsignedInteger('reserved_at')->nullable()->comment('وقت الحجز (unix timestamp)');
            $table->unsignedInteger('available_at')->comment('وقت توافر التنفيذ (unix timestamp)');
            $table->unsignedInteger('created_at')->comment('وقت الإنشاء (unix timestamp)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
