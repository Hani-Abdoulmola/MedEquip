<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول أقفال الكاش (lock) إذا استُخدمت آلية locks عبر DB
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary()->comment('مفتاح القفل (PK)');
            $table->string('owner')->comment('المالك/الجهاز الذي خلق القفل');
            $table->integer('expiration')->comment('وقت انتهاء القفل (unix timestamp)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
    }
};
