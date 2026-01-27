<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds notification enhancements to existing databases.
     * For fresh installs, these are already included in create_notifications_table migration.
     */
    public function up(): void
    {
        // Add parent_notification_id column to notifications table if it doesn't exist
        if (!Schema::hasColumn('notifications', 'parent_notification_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->uuid('parent_notification_id')->nullable()->after('id');
                $table->foreign('parent_notification_id')
                    ->references('id')
                    ->on('notifications')
                    ->onDelete('cascade');
                $table->index('parent_notification_id');
            });
        }

        // sent_notifications table has been removed - all notifications are now stored in notifications table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'parent_notification_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign(['parent_notification_id']);
                $table->dropIndex(['parent_notification_id']);
                $table->dropColumn('parent_notification_id');
            });
        }
    }
};
