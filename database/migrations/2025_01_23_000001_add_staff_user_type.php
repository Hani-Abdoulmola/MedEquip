<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserType;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the Staff user type to the user_types table.
     * Note: Currently, Staff users use user_type_id = 1 (Admin type) with role = Staff.
     * If you want Staff to be a separate user type, you'll need to update:
     * - UserController::create() and store() methods
     * - Any queries that filter by user_type_id
     */
    public function up(): void
    {
        // This migration is handled by ensure_user_types_order migration
        // Staff user type will be created/updated there with ID = 4
        // This migration is kept for backward compatibility but does nothing
        // to avoid duplicate entry errors
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Staff user type
        UserType::where('slug', 'staff')->delete();
    }
};
