<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // الصلاحيات الأساسية
        $permissions = [
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Suppliers
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',

            // Buyers
            'view buyers',
            'create buyers',
            'edit buyers',
            'delete buyers',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Orders
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',

            // Activity Logs
            'view activity logs',

            // RFQs (Request for Quotations)
            'view rfqs',
            'create rfqs',
            'edit rfqs',
            'delete rfqs',

            // Quotations
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // الأدوار الأساسية
        $roles = [
            'Admin' => $permissions, // كل الصلاحيات
            'Supplier' => [
                'view users',
                'view buyers',
                'view suppliers',
                'view products',
                'create products',
                'edit products',
                'view orders',
                'view activity logs',
                'view rfqs',
                'view quotations',
                'create quotations',
                'edit quotations',
            ],
            'Buyer' => [
                'view users',
                'view suppliers',
                'view products',
                'view orders',
                'create orders',
                'view activity logs',
                'view rfqs',
                'create rfqs',
                'edit rfqs',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
