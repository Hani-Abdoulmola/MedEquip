<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that UnifiedRolePermissionSeeder creates all required permissions.
     */
    public function test_seeder_creates_all_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        // Should have at least 87 permissions
        $permissionCount = Permission::count();
        $this->assertGreaterThanOrEqual(87, $permissionCount, 'Seeder should create at least 87 permissions');

        // All permissions should have Arabic names
        $permissionsWithoutArabic = Permission::whereNull('ar_name')->orWhere('ar_name', '')->count();
        $this->assertEquals(0, $permissionsWithoutArabic, 'All permissions should have Arabic names');
    }

    /**
     * Test that system roles are created correctly.
     */
    public function test_system_roles_are_created(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $adminRole = Role::where('name', 'Admin')->first();
        $supplierRole = Role::where('name', 'Supplier')->first();
        $buyerRole = Role::where('name', 'Buyer')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        $this->assertNotNull($adminRole, 'Admin role should exist');
        $this->assertNotNull($supplierRole, 'Supplier role should exist');
        $this->assertNotNull($buyerRole, 'Buyer role should exist');
        $this->assertNotNull($staffRole, 'Staff role should exist');

        // Check Arabic names
        $this->assertNotEmpty($adminRole->ar_name, 'Admin role should have Arabic name');
        $this->assertNotEmpty($supplierRole->ar_name, 'Supplier role should have Arabic name');
        $this->assertNotEmpty($buyerRole->ar_name, 'Buyer role should have Arabic name');
        $this->assertNotEmpty($staffRole->ar_name, 'Staff role should have Arabic name');
    }

    /**
     * Test that Admin role has all permissions.
     */
    public function test_admin_role_has_all_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $adminRole = Role::where('name', 'Admin')->first();
        $allPermissions = Permission::count();

        $this->assertEquals($allPermissions, $adminRole->permissions->count(), 
            'Admin role should have all permissions');
    }

    /**
     * Test that Supplier role has correct permissions.
     */
    public function test_supplier_role_has_correct_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $supplierRole = Role::where('name', 'Supplier')->first();
        $expectedPermissions = [
            'users.view',
            'buyers.view',
            'suppliers.view',
            'products.view',
            'products.create',
            'products.update',
            'orders.view',
            'activity_logs.view',
            'rfqs.view',
            'quotations.view',
            'quotations.submit',
            'quotations.update',
        ];

        $supplierPermissionNames = $supplierRole->permissions->pluck('name')->toArray();

        foreach ($expectedPermissions as $permission) {
            $this->assertContains($permission, $supplierPermissionNames, 
                "Supplier role should have {$permission} permission");
        }

        // Should have exactly 12 permissions
        $this->assertEquals(12, $supplierRole->permissions->count(), 
            'Supplier role should have exactly 12 permissions');
    }

    /**
     * Test that Buyer role has correct permissions.
     */
    public function test_buyer_role_has_correct_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $buyerRole = Role::where('name', 'Buyer')->first();
        $expectedPermissions = [
            'users.view',
            'suppliers.view',
            'products.view',
            'orders.view',
            'orders.create',
            'activity_logs.view',
            'rfqs.view',
            'rfqs.create',
            'rfqs.update',
            'rfqs.delete',
            'quotations.view',
            'quotations.accept',
            'quotations.reject',
            'quotations.compare',
            'invoices.view',
            'invoices.download',
            'deliveries.view',
        ];

        $buyerPermissionNames = $buyerRole->permissions->pluck('name')->toArray();

        foreach ($expectedPermissions as $permission) {
            $this->assertContains($permission, $buyerPermissionNames, 
                "Buyer role should have {$permission} permission");
        }

        // Should have exactly 17 permissions
        $this->assertEquals(17, $buyerRole->permissions->count(), 
            'Buyer role should have exactly 17 permissions');
    }

    /**
     * Test that Staff role has no default permissions.
     */
    public function test_staff_role_has_no_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $staffRole = Role::where('name', 'Staff')->first();

        $this->assertEquals(0, $staffRole->permissions->count(), 
            'Staff role should have no default permissions');
    }

    /**
     * Test that user can check permissions using can() method.
     */
    public function test_user_can_check_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $adminRole = Role::where('name', 'Admin')->first();
        $user = User::factory()->create();
        $user->assignRole($adminRole);

        // Admin should be able to check permissions
        $this->assertTrue($user->can('users.view'), 'Admin should have users.view permission');
        $this->assertTrue($user->can('invoices.view'), 'Admin should have invoices.view permission');
        $this->assertTrue($user->can('orders.view'), 'Admin should have orders.view permission');
    }

    /**
     * Test that Staff user with direct permissions can access resources.
     */
    public function test_staff_user_with_direct_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $staffRole = Role::where('name', 'Staff')->first();
        $usersViewPermission = Permission::where('name', 'users.view')->first();
        $productsViewPermission = Permission::where('name', 'products.view')->first();

        $user = User::factory()->create();
        $user->assignRole($staffRole);
        $user->givePermissionTo([$usersViewPermission, $productsViewPermission]);

        // User should have permissions via direct assignment
        $this->assertTrue($user->can('users.view'), 'Staff user should have users.view via direct permission');
        $this->assertTrue($user->can('products.view'), 'Staff user should have products.view via direct permission');
        $this->assertFalse($user->can('invoices.view'), 'Staff user should NOT have invoices.view');
    }

    /**
     * Test that AdminPermissionService filters correctly.
     */
    public function test_admin_permission_service_filters_supplier_buyer_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);

        $service = app(\App\Services\AdminPermissionService::class);
        $adminPermissions = $service->getAdminPermissions();

        // Should not include supplier/buyer specific permissions
        $supplierPermissions = ['suppliers.view', 'suppliers.create', 'suppliers.update'];
        $buyerPermissions = ['buyers.view', 'buyers.create', 'buyers.update'];

        $adminPermissionNames = $adminPermissions->pluck('name')->toArray();

        foreach ($supplierPermissions as $permission) {
            $this->assertNotContains($permission, $adminPermissionNames, 
                "Admin permissions should not include {$permission}");
        }

        foreach ($buyerPermissions as $permission) {
            $this->assertNotContains($permission, $adminPermissionNames, 
                "Admin permissions should not include {$permission}");
        }
    }
}

