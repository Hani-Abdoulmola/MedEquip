<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول مجموعات المهام (batches) إن استُخدمت Batch jobs
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary()->comment('معرف الباتش (UUID/string) - PK');
            $table->string('name')->comment('اسم الباتش');
            $table->integer('total_jobs')->comment('إجمالي المهام في الباتش');
            $table->integer('pending_jobs')->comment('المهام المعلقة');
            $table->integer('failed_jobs')->comment('المهام الفاشلة');
            $table->longText('failed_job_ids')->comment('قائمة معرفات المهام الفاشلة (serialized)');
            $table->mediumText('options')->nullable()->comment('خيارات الباتش (serialized/json)');
            $table->integer('cancelled_at')->nullable()->comment('وقت الإلغاء (unix timestamp)');
            $table->integer('created_at')->comment('وقت الإنشاء (unix timestamp)');
            $table->integer('finished_at')->nullable()->comment('وقت الانتهاء (unix timestamp)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_batches');
    }
};
