<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix RFQ status enum to include all required statuses.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE rfqs MODIFY COLUMN status ENUM('draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled') DEFAULT 'open'");
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE rfqs MODIFY COLUMN status ENUM('open', 'closed', 'cancelled') DEFAULT 'open'");
    }
};

