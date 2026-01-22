<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Services\AdminPermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Permission System Test Suite
 * 
 * Tests the integrity and correctness of the RBAC system
 */
class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions and roles
        $this->artisan('db:seed', ['--class' => 'UnifiedRolePermissionSeeder']);
    }

    /** @test */
    public function admin_user_has_zero_direct_permissions()
    {
        // Create admin user
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        // Admin should have ZERO direct permissions
        $this->assertCount(0, $admin->permissions, 
            'Admin user should have 0 direct permissions (all permissions via role)');
    }

    /** @test */
    public function admin_user_has_admin_role()
    {
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        // Admin should have Admin role
        $this->assertTrue($admin->hasRole('Admin'), 'Admin user must have Admin role');
    }

    /** @test */
    public function admin_role_has_all_permissions()
    {
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        $allPermissions = Permission::where('guard_name', 'web')->get();
        
        // Admin role should have all permissions
        $this->assertEquals(
            $allPermissions->count(),
            $adminRole->permissions->count(),
            'Admin role must have ALL permissions'
        );
    }

    /** @test */
    public function admin_user_can_access_all_permissions_via_role()
    {
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        $allPermissions = Permission::where('guard_name', 'web')->get();
        
        // Admin should have all permissions (via role)
        $effectivePermissions = $admin->getAllPermissions();
        
        $this->assertEquals(
            $allPermissions->count(),
            $effectivePermissions->count(),
            'Admin user must have access to all permissions via role'
        );
    }

    /** @test */
    public function staff_role_has_baseline_permissions()
    {
        $staffRole = Role::where('name', 'Staff')->where('guard_name', 'web')->first();
        
        // Staff role should have at least some baseline permissions
        $this->assertGreaterThan(0, $staffRole->permissions->count(), 
            'Staff role should have baseline permissions');
        
        // Staff should have view permissions
        $this->assertTrue($staffRole->hasPermissionTo('users.view'));
    }

    /** @test */
    public function new_user_inherits_role_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Staff');
        
        $staffRole = Role::where('name', 'Staff')->first();
        
        // User should inherit all role permissions
        $this->assertEquals(
            $staffRole->permissions->count(),
            $user->getAllPermissions()->count(),
            'New user should inherit all permissions from assigned role'
        );
    }

    /** @test */
    public function user_can_have_role_plus_direct_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Staff');
        
        $staffRolePermissionCount = Role::where('name', 'Staff')->first()->permissions->count();
        
        // Assign additional direct permission
        $additionalPermission = Permission::where('name', 'products.create')->first();
        $user->givePermissionTo($additionalPermission);
        
        // User should have role permissions + direct permission
        $this->assertGreaterThanOrEqual(
            $staffRolePermissionCount + 1,
            $user->getAllPermissions()->count(),
            'User should have role permissions + direct permissions'
        );
    }

    /** @test */
    public function admin_permission_service_filters_supplier_buyer_permissions()
    {
        $adminPermissionService = new AdminPermissionService();
        $adminPermissions = $adminPermissionService->getAdminPermissions();
        
        // Should not include supplier/buyer permissions
        $permissionNames = $adminPermissions->pluck('name')->toArray();
        
        $this->assertNotContains('suppliers.verify', $permissionNames);
        $this->assertNotContains('buyers.verify', $permissionNames);
        
        // Should include admin permissions
        $this->assertContains('users.view', $permissionNames);
        $this->assertContains('products.view', $permissionNames);
    }

    /** @test */
    public function cannot_assign_supplier_permissions_to_staff()
    {
        $supplierPermission = Permission::where('name', 'suppliers.verify')->first();
        
        $this->assertNotNull($supplierPermission, 'Supplier permission should exist');
        
        $adminPermissionService = new AdminPermissionService();
        $adminPermissionIds = $adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
        
        // Supplier permission should NOT be in admin permissions
        $this->assertNotContains($supplierPermission->id, $adminPermissionIds, 
            'Supplier permissions should be filtered out from admin permissions');
    }

    /** @test */
    public function critical_permissions_exist()
    {
        $criticalPermissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'permissions.view',
            'products.view',
            'orders.view',
        ];
        
        foreach ($criticalPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();
            
            $this->assertNotNull($permission, "Critical permission '{$permissionName}' must exist");
        }
    }

    /** @test */
    public function admin_can_access_critical_permissions()
    {
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
        
        $admin = User::where('email', 'admin@MedEquip.com')->first();
        
        $criticalPermissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'permissions.view',
        ];
        
        foreach ($criticalPermissions as $permissionName) {
            $this->assertTrue($admin->can($permissionName), 
                "Admin must have access to critical permission: {$permissionName}");
        }
    }

    /** @test */
    public function permissions_use_correct_guard()
    {
        $permissions = Permission::all();
        
        foreach ($permissions as $permission) {
            $this->assertEquals('web', $permission->guard_name, 
                "Permission '{$permission->name}' must use 'web' guard");
        }
    }

    /** @test */
    public function roles_use_correct_guard()
    {
        $roles = Role::all();
        
        foreach ($roles as $role) {
            $this->assertEquals('web', $role->guard_name, 
                "Role '{$role->name}' must use 'web' guard");
        }
    }

    /** @test */
    public function permission_names_use_dot_notation()
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        
        foreach ($permissions as $permission) {
            $this->assertStringContainsString('.', $permission->name, 
                "Permission '{$permission->name}' should use dot notation (module.action)");
        }
    }

    /** @test */
    public function sync_permissions_replaces_existing_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Staff');
        
        $permission1 = Permission::where('name', 'products.view')->first();
        $permission2 = Permission::where('name', 'orders.view')->first();
        
        // Assign first permission
        $user->syncPermissions([$permission1]);
        $this->assertCount(1, $user->permissions);
        $this->assertTrue($user->hasPermissionTo('products.view'));
        
        // Sync with second permission (should replace, not append)
        $user->syncPermissions([$permission2]);
        $this->assertCount(1, $user->permissions);
        $this->assertFalse($user->hasPermissionTo('products.view'));
        $this->assertTrue($user->hasPermissionTo('orders.view'));
    }

    /** @test */
    public function user_loses_role_permissions_when_role_removed()
    {
        $user = User::factory()->create();
        $user->assignRole('Staff');
        
        $initialPermissionCount = $user->getAllPermissions()->count();
        $this->assertGreaterThan(0, $initialPermissionCount);
        
        // Remove role
        $user->removeRole('Staff');
        
        // User should have zero effective permissions
        $this->assertCount(0, $user->getAllPermissions(), 
            'User should lose all role permissions when role is removed');
    }

    /** @test */
    public function role_permission_changes_affect_all_users_with_that_role()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $user1->assignRole('Staff');
        $user2->assignRole('Staff');
        
        $staffRole = Role::where('name', 'Staff')->first();
        $newPermission = Permission::where('name', 'products.create')->first();
        
        // Initially, users don't have this permission (assuming Staff role doesn't have it)
        // Add permission to Staff role
        $staffRole->givePermissionTo($newPermission);
        
        // Refresh users
        $user1->refresh();
        $user2->refresh();
        
        // Both users should now have the permission
        $this->assertTrue($user1->hasPermissionTo('products.create'));
        $this->assertTrue($user2->hasPermissionTo('products.create'));
    }

    /** @test */
    public function cannot_assign_multiple_internal_roles_to_user()
    {
        $user = User::factory()->create();
        
        $user->assignRole('Admin');
        $user->assignRole('Staff');
        
        // User should have both roles (Spatie allows multiple roles)
        // But our business logic should prevent this
        $this->assertEquals(2, $user->roles->count());
        
        // This test documents current behavior
        // TODO: Add validation to prevent multiple internal roles
    }

    /** @test */
    public function supplier_and_buyer_roles_have_fixed_permissions()
    {
        $supplierRole = Role::where('name', 'Supplier')->first();
        $buyerRole = Role::where('name', 'Buyer')->first();
        
        // These roles should have permissions defined in seeder
        $this->assertGreaterThan(0, $supplierRole->permissions->count());
        $this->assertGreaterThan(0, $buyerRole->permissions->count());
        
        // Supplier should have product permissions
        $this->assertTrue($supplierRole->hasPermissionTo('products.view'));
        $this->assertTrue($supplierRole->hasPermissionTo('products.create'));
        
        // Buyer should have RFQ permissions
        $this->assertTrue($buyerRole->hasPermissionTo('rfqs.create'));
    }
}
