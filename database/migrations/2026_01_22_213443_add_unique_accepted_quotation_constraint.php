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
     * Adds unique constraint to prevent multiple accepted quotations per RFQ.
     * This is CRITICAL for preventing race conditions when accepting quotations.
     * 
     * Note: We use a unique index with WHERE clause (partial index) which is supported
     * by PostgreSQL and MySQL 8.0.13+. For older MySQL, we'll create a trigger instead.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL supports partial unique indexes
            DB::statement('CREATE UNIQUE INDEX quotations_unique_accepted_per_rfq ON quotations(rfq_id) WHERE status = \'accepted\'');
        } elseif ($driver === 'mysql') {
            // MySQL 8.0.13+ supports functional indexes, but for compatibility,
            // we'll use a stored generated column approach
            try {
                // Try modern MySQL approach (8.0.13+)
                DB::statement('CREATE UNIQUE INDEX quotations_unique_accepted_per_rfq ON quotations(rfq_id, (CASE WHEN status = \'accepted\' THEN 1 END))');
            } catch (\Exception $e) {
                // Fallback: Add a virtual column and unique index on it
                Schema::table('quotations', function (Blueprint $table) {
                    // Add virtual column that's only set when status = 'accepted'
                    DB::statement('ALTER TABLE quotations ADD COLUMN rfq_accepted_check VARCHAR(255) GENERATED ALWAYS AS (IF(status = \'accepted\', CONCAT(rfq_id, \'-accepted\'), NULL)) STORED');
                    DB::statement('CREATE UNIQUE INDEX quotations_unique_accepted_per_rfq ON quotations(rfq_accepted_check)');
                });
            }
        } else {
            // SQLite and others: use unique index on concatenated field
            // This is less efficient but works
            Schema::table('quotations', function (Blueprint $table) {
                // We'll enforce this at application level for SQLite
                // Just add a regular index for performance
                $table->index(['rfq_id', 'status'], 'quotations_rfq_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS quotations_unique_accepted_per_rfq');
        } elseif ($driver === 'mysql') {
            // Try to drop the index
            try {
                DB::statement('DROP INDEX quotations_unique_accepted_per_rfq ON quotations');
            } catch (\Exception $e) {
                // If virtual column exists, drop it
                try {
                    DB::statement('ALTER TABLE quotations DROP COLUMN rfq_accepted_check');
                } catch (\Exception $e2) {
                    // Ignore if doesn't exist
                }
            }
        } else {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropIndex('quotations_rfq_status_idx');
            });
        }
    }
};
