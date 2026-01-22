<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

/**
 * Verify Admin permissions integrity
 * 
 * This command checks:
 * 1. Admin user exists
 * 2. Admin user has Admin role
 * 3. Admin user has ZERO direct permissions (all via role)
 * 4. Admin role has ALL permissions
 * 5. Admin user can access all permissions (effective check)
 */
class VerifyAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:verify-admin
                            {--fix : Automatically fix issues found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Admin user permissions integrity (ensures permissions come from role, not direct assignment)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Verifying Admin Permissions Integrity...');
        $this->newLine();

        $issues = [];
        $fixApplied = false;

        // 1. Check Admin user exists
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        if (!$admin) {
            $this->error('❌ Admin user not found (admin@MedEquip.com)');
            return self::FAILURE;
        }
        
        $this->info("✅ Admin user found: {$admin->email} (ID: {$admin->id})");

        // 2. Check Admin has Admin role
        if (!$admin->hasRole('Admin')) {
            $this->error('❌ Admin user does not have Admin role');
            $issues[] = 'missing_admin_role';
            
            if ($this->option('fix')) {
                $admin->assignRole('Admin');
                $this->info('   ✅ Fixed: Assigned Admin role');
                $fixApplied = true;
            }
        } else {
            $this->info('✅ Admin has Admin role');
        }

        // 3. Check Admin has ZERO direct permissions
        $directPermissionCount = $admin->permissions->count();
        
        if ($directPermissionCount > 0) {
            $this->error("❌ Admin has {$directPermissionCount} direct permissions (should be 0)");
            $this->warn('   Direct permissions: ' . $admin->permissions->pluck('name')->join(', '));
            $issues[] = 'has_direct_permissions';
            
            if ($this->option('fix')) {
                $admin->syncPermissions([]);
                $this->info('   ✅ Fixed: Removed all direct permissions');
                $fixApplied = true;
            }
        } else {
            $this->info('✅ Admin has 0 direct permissions (correct)');
        }

        // 4. Check Admin role has ALL permissions
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        
        if (!$adminRole) {
            $this->error('❌ Admin role not found');
            return self::FAILURE;
        }

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $rolePermissionCount = $adminRole->permissions->count();
        $totalPermissionCount = $allPermissions->count();

        if ($rolePermissionCount !== $totalPermissionCount) {
            $this->error("❌ Admin role has {$rolePermissionCount} permissions, but {$totalPermissionCount} exist");
            $issues[] = 'incomplete_role_permissions';
            
            if ($this->option('fix')) {
                $adminRole->syncPermissions($allPermissions);
                $this->info("   ✅ Fixed: Assigned all {$totalPermissionCount} permissions to Admin role");
                $fixApplied = true;
            }
        } else {
            $this->info("✅ Admin role has all {$totalPermissionCount} permissions");
        }

        // 5. Check effective permissions
        $effectivePermissionCount = $admin->getAllPermissions()->count();
        
        if ($effectivePermissionCount !== $totalPermissionCount) {
            $this->warn("⚠️  Admin effective permissions: {$effectivePermissionCount} (expected {$totalPermissionCount})");
            $issues[] = 'effective_permission_mismatch';
        } else {
            $this->info("✅ Admin has {$effectivePermissionCount} effective permissions (via role)");
        }

        // 6. Test critical permissions
        $this->newLine();
        $this->info('Testing critical permissions:');
        
        $criticalPermissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'permissions.view',
        ];

        foreach ($criticalPermissions as $permission) {
            if ($admin->can($permission)) {
                $this->info("   ✅ {$permission}");
            } else {
                $this->error("   ❌ {$permission} - ACCESS DENIED");
                $issues[] = "missing_{$permission}";
            }
        }

        // Summary
        $this->newLine();
        
        if (empty($issues)) {
            $this->info('🎉 All checks passed! Admin permissions are correctly configured.');
            return self::SUCCESS;
        } else {
            $this->error('❌ Issues found: ' . count($issues));
            
            if ($fixApplied) {
                $this->info('🔧 Fixes applied. Run this command again to verify.');
                app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            } else {
                $this->warn('💡 Run with --fix flag to automatically fix issues:');
                $this->warn('   php artisan permission:verify-admin --fix');
            }
            
            return self::FAILURE;
        }
    }
}
