<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id(); // PK: معرف نوع المستخدم
            $table->string('name', 50)->unique()->comment('اسم نوع المستخدم: Admin, Supplier, Buyer, ...');
            $table->string('slug', 50)->unique()->nullable()->comment('slug برمجي اختياري');
            $table->text('description')->nullable()->comment('وصف نوع المستخدم');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('حذف منطقي للسجل دون فقدان البيانات');

            // index للبحث السريع
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
