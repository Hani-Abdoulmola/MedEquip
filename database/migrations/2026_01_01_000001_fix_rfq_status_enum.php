<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fixes RFQ status enum to match validation rules.
     * Original enum: ['open', 'closed', 'cancelled']
     * New enum: ['draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled']
     */
    public function up(): void
    {
        // For MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rfqs MODIFY COLUMN status ENUM('draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled') DEFAULT 'open'");
        }
        
        // For PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE rfqs DROP CONSTRAINT IF EXISTS rfqs_status_check");
            DB::statement("ALTER TABLE rfqs ADD CONSTRAINT rfqs_status_check CHECK (status IN ('draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled'))");
        }
        
        // For SQLite (development only - enum is not enforced)
        // SQLite doesn't enforce enum constraints, so we just document it
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rfqs MODIFY COLUMN status ENUM('open', 'closed', 'cancelled') DEFAULT 'open'");
        }
        
        // For PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE rfqs DROP CONSTRAINT IF EXISTS rfqs_status_check");
            DB::statement("ALTER TABLE rfqs ADD CONSTRAINT rfqs_status_check CHECK (status IN ('open', 'closed', 'cancelled'))");
        }
    }
};

