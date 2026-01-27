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
     * Updates quotation status enum to include new states:
     * Old: ['pending', 'accepted', 'rejected']
     * New: ['draft', 'pending', 'revised', 'accepted', 'rejected', 'expired', 'withdrawn', 'converted']
     */
    public function up(): void
    {
        // For MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft', 'pending', 'revised', 'accepted', 'rejected', 'expired', 'withdrawn', 'converted') DEFAULT 'draft'");
        }
        
        // For PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE quotations DROP CONSTRAINT IF EXISTS quotations_status_check");
            DB::statement("ALTER TABLE quotations ADD CONSTRAINT quotations_status_check CHECK (status IN ('draft', 'pending', 'revised', 'accepted', 'rejected', 'expired', 'withdrawn', 'converted'))");
            DB::statement("ALTER TABLE quotations ALTER COLUMN status SET DEFAULT 'draft'");
        }
        
        // For SQLite (development) - enum not enforced
        // No action needed for SQLite
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'");
        }
        
        // For PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE quotations DROP CONSTRAINT IF EXISTS quotations_status_check");
            DB::statement("ALTER TABLE quotations ADD CONSTRAINT quotations_status_check CHECK (status IN ('pending', 'accepted', 'rejected'))");
            DB::statement("ALTER TABLE quotations ALTER COLUMN status SET DEFAULT 'pending'");
        }
    }
};
