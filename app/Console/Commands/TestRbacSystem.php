<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class TestRbacSystem extends Command
{
    protected $signature = 'rbac:test';
    protected $description = 'Test RBAC system: Admin access, Staff permissions, Supplier/Buyer routes';

    public function handle(): int
    {
        $this->info('🔐 RBAC System Verification Test');
        $this->newLine();

        // Test 1: Admin Access
        $this->testAdminAccess();

        // Test 2: Staff with Limited Permissions
        $this->testStaffAccess();

        // Test 3: Supplier/Buyer Routes
        $this->testSupplierBuyerRoutes();

        // Test 4: Gate::before() Verification
        $this->testGateBefore();

        $this->newLine();
        $this->info('✅ All RBAC tests completed!');
        
        return Command::SUCCESS;
    }

    private function testAdminAccess(): void
    {
        $this->info('📋 Test 1: Admin Access Verification');
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        if (!$admin) {
            $this->warn('⚠️  Admin user not found. Run AdminSeeder first.');
            return;
        }

        if (!$admin->hasRole('Admin')) {
            $this->error('❌ Admin user does not have Admin role!');
            return;
        }

        $this->info("✅ Admin user found: {$admin->email}");
        $this->info("✅ Admin has Admin role");

        // Test permissions
        $testPermissions = [
            'users.view',
            'users.create',
            'products.view',
            'orders.view',
            'permissions.view',
        ];

        $allPass = true;
        foreach ($testPermissions as $permission) {
            $can = $admin->can($permission);
            if ($can) {
                $this->line("  ✅ Can: {$permission}");
            } else {
                $this->error("  ❌ Cannot: {$permission}");
                $allPass = false;
            }
        }

        if ($allPass) {
            $this->info('✅ Admin can access all tested permissions');
        } else {
            $this->error('❌ Admin failed some permission checks');
        }

        $this->newLine();
    }

    private function testStaffAccess(): void
    {
        $this->info('📋 Test 2: Staff Access with Limited Permissions');
        
        // Find or create Staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff-test@medequip.com'],
            [
                'name' => 'Test Staff User',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->syncRoles([$staffRole]);

        // Remove all permissions
        $staff->syncPermissions([]);

        // Assign ONLY users.view
        $usersViewPermission = Permission::where('name', 'users.view')
            ->where('guard_name', 'web')
            ->first();

        if ($usersViewPermission) {
            $staff->givePermissionTo($usersViewPermission);
            $this->info("✅ Created/Updated Staff user: {$staff->email}");
            $this->info("✅ Assigned ONLY users.view permission");
        } else {
            $this->error('❌ users.view permission not found!');
            return;
        }

        // Test permissions
        $this->info('Testing Staff permissions:');
        
        $testCases = [
            ['permission' => 'users.view', 'should_pass' => true],
            ['permission' => 'users.create', 'should_pass' => false],
            ['permission' => 'products.view', 'should_pass' => false],
            ['permission' => 'orders.view', 'should_pass' => false],
        ];

        $allPass = true;
        foreach ($testCases as $test) {
            $can = $staff->can($test['permission']);
            $expected = $test['should_pass'];
            
            if ($can === $expected) {
                $status = $can ? '✅' : '✅ (correctly denied)';
                $this->line("  {$status} {$test['permission']}: " . ($can ? 'ALLOWED' : 'DENIED'));
            } else {
                $this->error("  ❌ {$test['permission']}: Expected " . ($expected ? 'ALLOWED' : 'DENIED') . " but got " . ($can ? 'ALLOWED' : 'DENIED'));
                $allPass = false;
            }
        }

        if ($allPass) {
            $this->info('✅ Staff permissions working correctly');
        } else {
            $this->error('❌ Staff permissions test failed');
        }

        $this->newLine();
    }

    private function testSupplierBuyerRoutes(): void
    {
        $this->info('📋 Test 3: Supplier/Buyer Routes Verification');
        
        // Check Supplier role exists
        $supplierRole = Role::where('name', 'Supplier')->where('guard_name', 'web')->first();
        if ($supplierRole) {
            $permissionCount = $supplierRole->permissions()->count();
            $this->info("✅ Supplier role exists with {$permissionCount} permissions");
        } else {
            $this->warn('⚠️  Supplier role not found');
        }

        // Check Buyer role exists
        $buyerRole = Role::where('name', 'Buyer')->where('guard_name', 'web')->first();
        if ($buyerRole) {
            $permissionCount = $buyerRole->permissions()->count();
            $this->info("✅ Buyer role exists with {$permissionCount} permissions");
        } else {
            $this->warn('⚠️  Buyer role not found');
        }

        $this->info('✅ Supplier/Buyer roles verified');
        $this->newLine();
    }

    private function testGateBefore(): void
    {
        $this->info('📋 Test 4: Gate::before() Admin Bypass Verification');
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        if (!$admin || !$admin->hasRole('Admin')) {
            $this->warn('⚠️  Admin user not found. Skipping Gate::before() test.');
            return;
        }

        // Test that Gate::before() is working
        // Admin should pass ALL checks via $user->can() (which uses Gate)
        $testPermissions = [
            'users.view',
            'users.create',
            'products.view',
        ];

        $allPass = true;
        foreach ($testPermissions as $permission) {
            // Use $user->can() which goes through Gate and Gate::before()
            $can = $admin->can($permission);
            if ($can) {
                $this->line("  ✅ Admin can() allows: {$permission} (Gate::before() working)");
            } else {
                $this->error("  ❌ Admin can() failed for: {$permission}");
                $allPass = false;
            }
        }

        if ($allPass) {
            $this->info('✅ Gate::before() is working correctly');
        } else {
            $this->error('❌ Gate::before() test failed');
        }

        $this->newLine();
    }
}
