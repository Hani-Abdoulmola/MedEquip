<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropOrphanedTable extends Command
{
    protected $signature = 'db:drop-orphaned-table';
    protected $description = 'Drop orphaned rfq_template_items table';

    public function handle(): int
    {
        $tables = ['rfq_template_items', 'supplier_performance_metrics'];
        
        try {
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    Schema::dropIfExists($table);
                    $this->info("✅ Dropped {$table} table successfully");
                }
            }
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to drop table: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
