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
        Schema::create('permission_audits', function (Blueprint $table) {
            $table->id();
            
            // Who made the change
            $table->foreignId('admin_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // What was changed
            $table->enum('action', ['assigned', 'revoked', 'synced', 'template_applied', 'bulk_assigned', 'role_updated']);
            $table->string('entity_type'); // 'user' or 'role'
            $table->unsignedBigInteger('entity_id'); // user_id or role_id
            
            // Target user/role name (for quick reference)
            $table->string('entity_name')->nullable();
            
            // Permission details
            $table->json('permissions_added')->nullable(); // Array of permission names
            $table->json('permissions_removed')->nullable(); // Array of permission names
            $table->integer('permissions_count')->default(0);
            
            // Additional context
            $table->string('template_name')->nullable(); // If template was applied
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Any additional data
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['entity_type', 'entity_id']);
            $table->index('admin_user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_audits');
    }
};
