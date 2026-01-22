<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminType = UserType::where('slug', 'admin')->first();

        if (!$adminType) {
            $this->command->warn('⚠️  Admin user type not found. Run UserTypeSeeder first.');
        }

        // Find or create admin user (handle different email variations)
        $possibleEmails = [
            'admin@MedEquip.com',
            'admin@medequip.com',
            'superadmin@medequip.com',
            'superadmin@MedEquip.com',
        ];

        $admin = null;
        foreach ($possibleEmails as $email) {
            $admin = User::where('email', $email)->first();
            if ($admin) {
                break;
            }
        }

        if (!$admin) {
            $admin = User::create([
                'user_type_id' => $adminType?->id,
                'name' => 'System Administrator',
                'email' => 'admin@MedEquip.com',
                'phone' => '0910000000',
                'password' => Hash::make('1234567890'),
                'status' => 'active',
            ]);
        } else {
            // Update existing admin
            $admin->update([
                'user_type_id' => $adminType?->id ?? $admin->user_type_id,
                'name' => 'System Administrator',
                'status' => 'active',
            ]);
        }

        // Ensure Admin role exists
        $adminRole = \App\Models\Role::where('name', 'Admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$adminRole) {
            $this->command->error('❌ Admin role not found! Run UnifiedRolePermissionSeeder first.');
            return;
        }

        // Assign Admin role
        $admin->syncRoles(['Admin']);

        // Get ALL permissions
        $allPermissions = Permission::where('guard_name', 'web')->get();

        if ($allPermissions->count() === 0) {
            $this->command->error('❌ No permissions found! Run UnifiedRolePermissionSeeder first.');
            return;
        }

        // CRITICAL: Ensure Admin ROLE has all permissions
        // Admin user inherits permissions from role (NOT direct assignment)
        $adminRole->syncPermissions($allPermissions);

        // DO NOT assign direct permissions to admin user
        // Admin authority comes from Admin role, not user instance

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Verify
        $this->command->info("✅ Admin user synced: {$admin->email} (ID: {$admin->id})");
        $this->command->info("✅ Roles: " . $admin->roles->pluck('name')->join(', '));
        $this->command->info("✅ Direct permissions: {$admin->permissions->count()} (should be 0)");
        $this->command->info("✅ Role permissions: {$adminRole->permissions->count()}");
        $this->command->info("✅ Total effective permissions: {$admin->getAllPermissions()->count()}");
        
        // Final verification
        if ($admin->can('users.view')) {
            $this->command->info("✅ Permission check passed: users.view");
        } else {
            $this->command->error("❌ Permission check FAILED: users.view");
        }
    }
}
