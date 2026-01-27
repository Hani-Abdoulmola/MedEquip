<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drop sent_notifications table - all notifications are now stored in the notifications table.
     */
    public function up(): void
    {
        Schema::dropIfExists('sent_notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate sent_notifications table if needed (for rollback)
        Schema::create('sent_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('type')->default('info');
            $table->string('recipient_type');
            $table->json('recipient_ids')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('read_count')->default(0);
            $table->integer('unread_count')->default(0);
            $table->timestamps();
            
            $table->index('sender_id');
            $table->index('created_at');
        });
    }
};
