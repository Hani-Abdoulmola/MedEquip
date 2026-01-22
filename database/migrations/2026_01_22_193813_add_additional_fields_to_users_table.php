<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('phone')->comment('📍 عنوان المستخدم');
            $table->string('city', 100)->nullable()->after('address')->comment('🏙️ المدينة');
            $table->string('country', 100)->nullable()->after('city')->comment('🌍 الدولة');
            $table->text('notes')->nullable()->after('country')->comment('📝 ملاحظات إدارية');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'country', 'notes']);
        });
    }
};
