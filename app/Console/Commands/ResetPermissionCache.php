<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

/**
 * Reset permission cache
 * 
 * Use this command after:
 * - Deploying permission changes
 * - Running seeders
 * - Manual database changes to permissions/roles
 */
class ResetPermissionCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:cache-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Spatie permission cache (use after seeding or permission changes)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        $this->info('✅ Permission cache cleared successfully');
        $this->info('💡 All users will now see updated permissions');
        
        return self::SUCCESS;
    }
}
