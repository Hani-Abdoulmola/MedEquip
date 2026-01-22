<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

class ValidatePermissions extends Command
{
    protected $signature = 'permissions:validate';
    protected $description = 'Validate that all route permission middleware matches database permissions';

    public function handle(): int
    {
        $this->info('🔍 Validating route permissions against database...');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Get all permissions from database
        $dbPermissions = Permission::where('guard_name', 'web')->pluck('name')->toArray();
        
        $count = count($dbPermissions);
        $this->info("📊 Database has {$count} permissions");
        $this->newLine();

        // Extract permissions from routes
        $routePermissions = [];
        $missingPermissions = [];
        $validPermissions = [];

        foreach (Route::getRoutes() as $route) {
            $middleware = $route->gatherMiddleware();
            
            foreach ($middleware as $mid) {
                // Check if it's a permission middleware
                if (is_string($mid) && str_starts_with($mid, 'permission:')) {
                    // Extract permission name(s)
                    $permString = substr($mid, 11); // Remove 'permission:'
                    $perms = explode('|', $permString);
                    
                    foreach ($perms as $perm) {
                        $perm = trim($perm);
                        $routePermissions[] = [
                            'permission' => $perm,
                            'route' => $route->getName() ?: $route->uri(),
                            'method' => implode('|', $route->methods()),
                        ];
                        
                        // Check if permission exists in database
                        if (!in_array($perm, $dbPermissions)) {
                            $missingPermissions[] = [
                                'permission' => $perm,
                                'route' => $route->getName() ?: $route->uri(),
                            ];
                        } else {
                            $validPermissions[] = $perm;
                        }
                    }
                }
            }
        }

        // Display results
        $uniqueRoutePerms = collect($routePermissions)->pluck('permission')->unique()->sort()->values();
        $this->info("🔑 Routes use {$uniqueRoutePerms->count()} unique permissions");
        $this->newLine();

        if (empty($missingPermissions)) {
            $this->info('✅ ALL ROUTE PERMISSIONS EXIST IN DATABASE!');
            $this->newLine();
            
            // Show breakdown
            $this->table(
                ['Permission', 'Status'],
                $uniqueRoutePerms->take(10)->map(function($perm) {
                    return [$perm, '✅ Valid'];
                })
            );
            
            if ($uniqueRoutePerms->count() > 10) {
                $this->line("... and " . ($uniqueRoutePerms->count() - 10) . " more");
            }
            
            return 0;
        }

        // Show errors
        $this->error("❌ FOUND {count($missingPermissions)} PERMISSION MISMATCHES!");
        $this->newLine();
        
        $this->table(
            ['Missing Permission', 'Used in Route'],
            collect($missingPermissions)->unique('permission')->map(function($item) {
                return [$item['permission'], $item['route']];
            })
        );

        $this->newLine();
        $this->warn('🔧 SUGGESTED FIXES:');
        $this->newLine();
        
        foreach (collect($missingPermissions)->unique('permission') as $missing) {
            $perm = $missing['permission'];
            
            // Try to find similar permissions
            $similar = collect($dbPermissions)->filter(function($dbPerm) use ($perm) {
                similar_text($perm, $dbPerm, $percent);
                return $percent > 70;
            })->first();
            
            if ($similar) {
                $this->line("  • Change '{$perm}' → '{$similar}'");
            } else {
                $this->line("  • Add '{$perm}' to UnifiedRolePermissionSeeder.php");
            }
        }

        return 1;
    }
}
