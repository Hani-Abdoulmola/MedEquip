<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الكاش عند استخدام cache driver = database
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary()->comment('مفتاح الكاش (PK)');
            $table->mediumText('value')->comment('قيمة الكاش (مسلسلة/مشفّرة)');
            $table->integer('expiration')->nullable()->comment('وقت انتهاء الكاش (unix timestamp) - nullable للدلالات الخاصة');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
    }
};
