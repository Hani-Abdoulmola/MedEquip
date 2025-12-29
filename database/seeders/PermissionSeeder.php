<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates all atomic permissions for the system.
     * Admin role gets all permissions automatically.
     * Staff roles get permissions assigned individually.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all atomic permissions grouped by module
        $permissions = [
            // Users Module
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage_permissions', // Special permission for assigning permissions

            // Suppliers Module
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',
            'suppliers.verify',
            'suppliers.toggle_active',

            // Buyers Module
            'buyers.view',
            'buyers.create',
            'buyers.update',
            'buyers.delete',
            'buyers.verify',
            'buyers.toggle_active',

            // RFQs Module
            'rfqs.view',
            'rfqs.create',
            'rfqs.update',
            'rfqs.delete',
            'rfqs.publish',
            'rfqs.assign_suppliers',
            'rfqs.update_status',
            'rfqs.toggle_visibility',

            // Quotations Module
            'quotations.view',
            'quotations.submit',
            'quotations.update',
            'quotations.delete',
            'quotations.accept',
            'quotations.reject',
            'quotations.compare',

            // Orders Module
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            'orders.confirm',
            'orders.update_status',

            // Invoices Module
            'invoices.view',
            'invoices.create',
            'invoices.update',
            'invoices.delete',
            'invoices.approve',
            'invoices.download',
            'invoices.export',

            // Payments Module
            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',
            'payments.export',

            // Deliveries Module
            'deliveries.view',
            'deliveries.create',
            'deliveries.update',
            'deliveries.delete',
            'deliveries.update_status',
            'deliveries.verify',
            'deliveries.upload_proof',

            // Products Module
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.approve',
            'products.reject',
            'products.request_changes',

            // Manufacturers Module
            'manufacturers.view',
            'manufacturers.create',
            'manufacturers.update',
            'manufacturers.delete',

            // Product Categories Module
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            // Activity Logs Module
            'activity_logs.view',
            'activity_logs.delete',

            // Notifications Module
            'notifications.view',
            'notifications.create',
            'notifications.update',
            'notifications.delete',

            // Settings Module
            'settings.view',
            'settings.update',

            // Reports Module
            'reports.view',
            'reports.export',

            // Roles & Permissions Management (Admin only)
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Get all permissions for Admin role
        $allPermissions = Permission::where('guard_name', 'web')->pluck('name');

        // Create or update Admin role with all permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web']
        );
        $adminRole->syncPermissions($allPermissions);

        // Create Supplier role (permissions assigned via business logic, not here)
        Role::firstOrCreate(
            ['name' => 'Supplier', 'guard_name' => 'web'],
            ['name' => 'Supplier', 'guard_name' => 'web']
        );

        // Create Buyer role (permissions assigned via business logic, not here)
        Role::firstOrCreate(
            ['name' => 'Buyer', 'guard_name' => 'web'],
            ['name' => 'Buyer', 'guard_name' => 'web']
        );

        // Create Staff role (permissions assigned individually by admin)
        Role::firstOrCreate(
            ['name' => 'Staff', 'guard_name' => 'web'],
            ['name' => 'Staff', 'guard_name' => 'web']
        );

        $this->command->info('✅ Created ' . count($permissions) . ' permissions');
        $this->command->info('✅ Created/Updated Admin, Supplier, Buyer, and Staff roles');
        $this->command->info('✅ Assigned all permissions to Admin role');
    }
}

