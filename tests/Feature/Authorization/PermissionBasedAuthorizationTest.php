<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionBasedAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed permissions
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
    }

    #[Test]
    public function admin_has_all_permissions_and_can_access_all_routes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        // Test various routes that require permissions
        $this->get(route('admin.users'))->assertStatus(200);
        $this->get(route('admin.roles.index'))->assertStatus(200);
        $this->get(route('admin.permissions.index'))->assertStatus(200);
        $this->get(route('admin.suppliers'))->assertStatus(200);
        $this->get(route('admin.buyers'))->assertStatus(200);
    }

    #[Test]
    public function staff_without_permission_cannot_access_protected_routes(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $this->actingAs($staff);

        // Staff without users.view permission should get 403
        $this->get(route('admin.users'))->assertStatus(403);
        $this->get(route('admin.roles.index'))->assertStatus(403);
        $this->get(route('admin.permissions.index'))->assertStatus(403);
    }

    #[Test]
    public function staff_with_permission_can_access_protected_routes(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        // Assign specific permissions
        $usersViewPermission = Permission::where('name', 'users.view')->first();
        $rolesViewPermission = Permission::where('name', 'roles.view')->first();
        
        $staff->givePermissionTo([$usersViewPermission, $rolesViewPermission]);

        $this->actingAs($staff);

        // Staff with permissions should be able to access
        $this->get(route('admin.users'))->assertStatus(200);
        $this->get(route('admin.roles.index'))->assertStatus(200);
        
        // But not routes they don't have permission for
        $this->get(route('admin.permissions.index'))->assertStatus(403);
    }

    #[Test]
    public function staff_cannot_create_users_without_permission(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        // Only give view permission, not create
        $usersViewPermission = Permission::where('name', 'users.view')->first();
        $staff->givePermissionTo($usersViewPermission);

        $this->actingAs($staff);

        // Can view but cannot create
        $this->get(route('admin.users'))->assertStatus(200);
        $this->get(route('admin.users.create'))->assertStatus(403);
        
        $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'user_type_id' => 1,
            'status' => 'active',
        ])->assertStatus(403);
    }

    #[Test]
    public function staff_can_create_users_with_permission(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        // Give both view and create permissions
        $usersViewPermission = Permission::where('name', 'users.view')->first();
        $usersCreatePermission = Permission::where('name', 'users.create')->first();
        $staff->givePermissionTo([$usersViewPermission, $usersCreatePermission]);

        $this->actingAs($staff);

        // Can view and create
        $this->get(route('admin.users.create'))->assertStatus(200);
        
        $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type_id' => 1,
            'status' => 'active',
        ])->assertRedirect(route('admin.users'));
    }

    #[Test]
    public function admin_can_assign_permissions_to_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $this->actingAs($admin);

        $permission = Permission::where('name', 'users.view')->first();

        $this->put(route('admin.users.update-permissions', $staff), [
            'permissions' => [$permission->id],
        ])->assertRedirect(route('admin.users.edit', $staff));

        $this->assertTrue($staff->fresh()->hasPermissionTo('users.view'));
    }

    #[Test]
    public function admin_can_create_roles_with_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $permission = Permission::where('name', 'users.view')->first();

        $this->post(route('admin.roles.store'), [
            'name' => 'Custom Role',
            'permissions' => [$permission->id],
        ])->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'Custom Role']);
        
        $role = Role::where('name', 'Custom Role')->first();
        $this->assertTrue($role->hasPermissionTo('users.view'));
    }

    #[Test]
    public function admin_can_update_role_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);
        $permission1 = Permission::where('name', 'users.view')->first();
        $permission2 = Permission::where('name', 'users.create')->first();
        
        $role->givePermissionTo($permission1);

        $this->actingAs($admin);

        $this->put(route('admin.roles.update', $role), [
            'name' => 'Test Role',
            'permissions' => [$permission1->id, $permission2->id],
        ])->assertRedirect(route('admin.roles.index'));

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertTrue($role->hasPermissionTo('users.create'));
    }

    #[Test]
    public function system_roles_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $adminRole = Role::where('name', 'Admin')->first();
        
        $this->delete(route('admin.roles.destroy', $adminRole))
            ->assertRedirect()
            ->assertSessionHasErrors('error');

        $this->assertDatabaseHas('roles', ['name' => 'Admin']);
    }

    #[Test]
    public function custom_roles_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $customRole = Role::create(['name' => 'Custom Role', 'guard_name' => 'web']);

        $this->delete(route('admin.roles.destroy', $customRole))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('roles', ['name' => 'Custom Role']);
    }

    #[Test]
    public function users_can_view_their_own_profile_regardless_of_permissions(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        // No permissions assigned
        $this->actingAs($staff);

        // Should be able to view own profile even without users.view permission
        $this->get(route('admin.users.show', $staff))->assertStatus(200);
    }

    #[Test]
    public function users_cannot_view_other_users_profiles_without_permission(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $otherUser = User::factory()->create();

        $this->actingAs($staff);

        // Cannot view other user's profile without permission
        $this->get(route('admin.users.show', $otherUser))->assertStatus(403);
    }

    #[Test]
    public function users_can_update_their_own_profile_regardless_of_permissions(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $this->actingAs($staff);

        // Should be able to update own profile even without users.update permission
        $this->put(route('admin.users.update', $staff), [
            'name' => 'Updated Name',
            'email' => $staff->email,
            'phone' => $staff->phone,
            'user_type_id' => $staff->user_type_id,
            'status' => $staff->status,
        ])->assertRedirect(route('admin.users'));
    }

    #[Test]
    public function users_cannot_update_other_users_without_permission(): void
    {
        $staff = User::factory()->create();
        $staffRole = Role::create(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $otherUser = User::factory()->create();

        $this->actingAs($staff);

        // Cannot update other user without permission
        $this->put(route('admin.users.update', $otherUser), [
            'name' => 'Updated Name',
            'email' => $otherUser->email,
            'phone' => $otherUser->phone,
            'user_type_id' => $otherUser->user_type_id,
            'status' => $otherUser->status,
        ])->assertStatus(403);
    }
}

