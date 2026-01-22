<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

class DiagnosePermissions extends Command
{
    protected $signature = 'permissions:diagnose {email}';
    protected $description = 'Diagnose permission issues for a user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('❌ User not found!');
            return 1;
        }

        $this->info('🔍 Diagnosing permissions for: ' . $user->email);
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Check roles
        $this->info('📋 ROLES:');
        $roles = $user->roles;
        if ($roles->count() === 0) {
            $this->warn('  ⚠️  No roles assigned!');
        } else {
            foreach ($roles as $role) {
                $this->line("  ✅ {$role->name} (guard: {$role->guard_name})");
                $this->line("     Permissions: {$role->permissions->count()}");
            }
        }
        $this->newLine();

        // Check direct permissions
        $this->info('🔑 DIRECT PERMISSIONS:');
        $directPerms = $user->permissions;
        $this->line("  Count: {$directPerms->count()}");
        if ($directPerms->count() > 0) {
            $sample = $directPerms->take(5)->pluck('name')->join(', ');
            $this->line("  Sample: {$sample}...");
        } else {
            $this->warn('  ⚠️  No direct permissions assigned!');
        }
        $this->newLine();

        // Check ALL permissions (via roles + direct)
        $this->info('✅ TOTAL EFFECTIVE PERMISSIONS:');
        $allPerms = $user->getAllPermissions();
        $this->line("  Count: {$allPerms->count()}");
        
        if ($allPerms->count() === 0) {
            $this->error('  ❌ USER HAS NO PERMISSIONS AT ALL!');
            $this->error('  This is the root cause of authorization failures.');
        } else {
            $this->line("  Status: ✅ User has permissions");
        }
        $this->newLine();

        // Test specific critical permissions
        $this->info('🧪 TESTING CRITICAL PERMISSIONS:');
        $testPermissions = [
            'users.view',
            'suppliers.view',
            'products.view',
            'orders.view',
        ];

        $passCount = 0;
        $failCount = 0;

        foreach ($testPermissions as $perm) {
            $hasPermissionTo = $user->hasPermissionTo($perm);
            $canCheck = $user->can($perm);
            $hasDirectPermission = $user->hasDirectPermission($perm);

            if ($hasPermissionTo && $canCheck) {
                $this->line("  ✅ {$perm}");
                $this->line("     hasPermissionTo(): PASS | can(): PASS | direct: " . ($hasDirectPermission ? 'YES' : 'NO'));
                $passCount++;
            } else {
                $this->error("  ❌ {$perm}");
                $this->error("     hasPermissionTo(): " . ($hasPermissionTo ? 'PASS' : 'FAIL'));
                $this->error("     can(): " . ($canCheck ? 'PASS' : 'FAIL'));
                $this->error("     direct: " . ($hasDirectPermission ? 'YES' : 'NO'));
                $failCount++;
            }
        }
        $this->newLine();

        // Check cache
        $this->info('💾 CACHE STATUS:');
        $cacheKey = config('permission.cache.key');
        $cacheStore = config('permission.cache.store');
        $this->line("  Key: {$cacheKey}");
        $this->line("  Store: {$cacheStore}");
        
        $cacheExists = cache()->has($cacheKey);
        if ($cacheExists) {
            $this->line("  Status: ✅ Cache exists");
            $cachedData = cache()->get($cacheKey);
            if (is_array($cachedData)) {
                $this->line("  Cached permissions: " . count($cachedData));
            }
        } else {
            $this->warn("  Status: ⚠️  Cache is empty (will be built on next check)");
        }
        $this->newLine();

        // Check guard
        $this->info('🛡️  GUARD VERIFICATION:');
        $userGuard = config('auth.defaults.guard');
        $this->line("  Default guard: {$userGuard}");
        $this->line("  Permission guard: web");
        
        if ($userGuard !== 'web') {
            $this->warn("  ⚠️  Guard mismatch detected!");
            $this->warn("  User guard ({$userGuard}) != Permission guard (web)");
        } else {
            $this->line("  Status: ✅ Guards match");
        }
        $this->newLine();

        // Database verification
        $this->info('🗄️  DATABASE VERIFICATION:');
        $totalPermissions = Permission::where('guard_name', 'web')->count();
        $totalRoles = Role::where('guard_name', 'web')->count();
        $this->line("  Total permissions in DB: {$totalPermissions}");
        $this->line("  Total roles in DB: {$totalRoles}");

        // Check pivot tables
        $rolePerms = \DB::table('role_has_permissions')
            ->whereIn('role_id', $user->roles->pluck('id'))
            ->count();
        $modelPerms = \DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->count();

        $this->line("  Role permissions (pivot): {$rolePerms}");
        $this->line("  Direct permissions (pivot): {$modelPerms}");
        $this->newLine();

        // Final diagnosis
        $this->info('🎯 DIAGNOSIS SUMMARY:');
        $this->info('═══════════════════════════════════════════════════════');
        
        if ($passCount === count($testPermissions)) {
            $this->info('✅ ALL TESTS PASSED - Permissions working correctly!');
        } else {
            $this->error("❌ {$failCount} TESTS FAILED - Permissions not working!");
            $this->newLine();
            $this->warn('🔧 RECOMMENDED FIXES:');
            
            if ($allPerms->count() === 0) {
                $this->warn('1. Run: php artisan db:seed --class=AdminSeeder');
            }
            
            if (!$cacheExists || $failCount > 0) {
                $this->warn('2. Run: php artisan permission:cache-reset');
            }
            
            $this->warn('3. Run: php artisan cache:clear');
            $this->warn('4. Run this diagnostic again');
        }

        return $passCount === count($testPermissions) ? 0 : 1;
    }
}
