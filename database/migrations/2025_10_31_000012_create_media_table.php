<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول Spatie Media Library
        Schema::create('media', function (Blueprint $table) {
            $table->id(); // PK
            $table->morphs('model'); // model_id, model_type (polymorphic relation)
            $table->uuid()->nullable()->unique()->comment('UUID اختياري للربط الخارجي');
            $table->string('collection_name')->comment('اسم مجموعة الميديا');
            $table->string('name')->comment('اسم العنصر داخل المجموعة');
            $table->string('file_name')->comment('اسم الملف على القرص');
            $table->string('mime_type')->nullable()->comment('نوع MIME');
            $table->string('disk')->comment('القرص/الـ storage disk المستخدم');
            $table->string('conversions_disk')->nullable()->comment('قرص التحويلات إن وُجد');
            $table->unsignedBigInteger('size')->comment('حجم الملف بالبايت');
            $table->json('manipulations')->nullable()->comment('الإجراءات/التعديلات (json)');
            $table->json('custom_properties')->nullable()->comment('خصائص مخصصة (json)');
            $table->json('generated_conversions')->nullable()->comment('التحويلات المولّدة (json)');
            $table->json('responsive_images')->nullable()->comment('صور responsive (json)');
            $table->unsignedInteger('order_column')->nullable()->index()->comment('ترتيب الملف داخل المجموعة');
            $table->nullableTimestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
