<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CRITICAL FIX: Standardize foreign key constraints for product_categories table
     * Change restrictOnDelete() to nullOnDelete() to match products table behavior
     * This ensures consistent behavior when deleting users who created/updated categories
     */
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            // Recreate with nullOnDelete() for consistency
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->comment('👤 المستخدم الذي أنشأ الفئة');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->comment('✏️ آخر من قام بتعديل الفئة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            // Restore original restrictOnDelete() behavior
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete()
                ->comment('👤 المستخدم الذي أنشأ الفئة');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete()
                ->comment('✏️ آخر من قام بتعديل الفئة');
        });
    }
};
