<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

/**
 * Unified Seeder for Roles and Permissions
 * 
 * This seeder:
 * 1. Creates all atomic permissions with Arabic names
 * 2. Creates system roles (Admin, Supplier, Buyer, Staff)
 * 3. Assigns permissions to roles based on business logic
 * 
 * This replaces the need for both PermissionSeeder and RolePermissionSeeder.
 * Run this seeder instead: php artisan db:seed --class=UnifiedRolePermissionSeeder
 */
class UnifiedRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all atomic permissions with Arabic names
        $permissions = [
            // Users Module
            'users.view' => 'عرض المستخدمين',
            'users.create' => 'إنشاء مستخدمين',
            'users.update' => 'تعديل المستخدمين',
            'users.delete' => 'حذف المستخدمين',
            'users.manage_permissions' => 'إدارة صلاحيات المستخدمين',

            // Suppliers Module
            'suppliers.view' => 'عرض الموردين',
            'suppliers.create' => 'إنشاء موردين',
            'suppliers.update' => 'تعديل الموردين',
            'suppliers.delete' => 'حذف الموردين',
            'suppliers.verify' => 'التحقق من الموردين',
            'suppliers.toggle_active' => 'تفعيل/تعطيل الموردين',

            // Buyers Module
            'buyers.view' => 'عرض المشترين',
            'buyers.create' => 'إنشاء مشترين',
            'buyers.update' => 'تعديل المشترين',
            'buyers.delete' => 'حذف المشترين',
            'buyers.verify' => 'التحقق من المشترين',
            'buyers.toggle_active' => 'تفعيل/تعطيل المشترين',

            // RFQs Module
            'rfqs.view' => 'عرض طلبات عروض الأسعار',
            'rfqs.create' => 'إنشاء طلبات عروض أسعار',
            'rfqs.update' => 'تعديل طلبات عروض الأسعار',
            'rfqs.delete' => 'حذف طلبات عروض الأسعار',
            'rfqs.publish' => 'نشر طلبات عروض الأسعار',
            'rfqs.assign_suppliers' => 'تعيين موردين لطلبات عروض الأسعار',
            'rfqs.update_status' => 'تحديث حالة طلبات عروض الأسعار',
            'rfqs.toggle_visibility' => 'إظهار/إخفاء طلبات عروض الأسعار',

            // Quotations Module
            'quotations.view' => 'عرض عروض الأسعار',
            'quotations.submit' => 'تقديم عروض أسعار',
            'quotations.update' => 'تعديل عروض الأسعار',
            'quotations.delete' => 'حذف عروض الأسعار',
            'quotations.accept' => 'قبول عروض الأسعار',
            'quotations.reject' => 'رفض عروض الأسعار',
            'quotations.compare' => 'مقارنة عروض الأسعار',

            // Orders Module
            'orders.view' => 'عرض الطلبات',
            'orders.create' => 'إنشاء طلبات',
            'orders.update' => 'تعديل الطلبات',
            'orders.delete' => 'حذف الطلبات',
            'orders.confirm' => 'تأكيد الطلبات',
            'orders.update_status' => 'تحديث حالة الطلبات',

            // Invoices Module
            'invoices.view' => 'عرض الفواتير',
            'invoices.create' => 'إنشاء فواتير',
            'invoices.update' => 'تعديل الفواتير',
            'invoices.delete' => 'حذف الفواتير',
            'invoices.approve' => 'الموافقة على الفواتير',
            'invoices.download' => 'تحميل الفواتير',
            'invoices.export' => 'تصدير الفواتير',

            // Payments Module
            'payments.view' => 'عرض المدفوعات',
            'payments.create' => 'إنشاء مدفوعات',
            'payments.update' => 'تعديل المدفوعات',
            'payments.delete' => 'حذف المدفوعات',
            'payments.export' => 'تصدير المدفوعات',

            // Deliveries Module
            'deliveries.view' => 'عرض عمليات التسليم',
            'deliveries.create' => 'إنشاء عمليات تسليم',
            'deliveries.update' => 'تعديل عمليات التسليم',
            'deliveries.delete' => 'حذف عمليات التسليم',
            'deliveries.update_status' => 'تحديث حالة عمليات التسليم',
            'deliveries.verify' => 'التحقق من عمليات التسليم',
            'deliveries.upload_proof' => 'رفع إثبات التسليم',

            // Products Module
            'products.view' => 'عرض المنتجات',
            'products.create' => 'إنشاء منتجات',
            'products.update' => 'تعديل المنتجات',
            'products.delete' => 'حذف المنتجات',
            'products.approve' => 'الموافقة على المنتجات',
            'products.reject' => 'رفض المنتجات',
            'products.request_changes' => 'طلب تعديلات على المنتجات',

            // Manufacturers Module
            'manufacturers.view' => 'عرض الشركات المصنعة',
            'manufacturers.create' => 'إنشاء شركات مصنعة',
            'manufacturers.update' => 'تعديل الشركات المصنعة',
            'manufacturers.delete' => 'حذف الشركات المصنعة',

            // Product Categories Module
            'categories.view' => 'عرض الفئات',
            'categories.create' => 'إنشاء فئات',
            'categories.update' => 'تعديل الفئات',
            'categories.delete' => 'حذف الفئات',

            // Activity Logs Module
            'activity_logs.view' => 'عرض سجل النشاط',
            'activity_logs.delete' => 'حذف سجل النشاط',

            // Notifications Module
            'notifications.view' => 'عرض الإشعارات',
            'notifications.create' => 'إنشاء إشعارات',
            'notifications.update' => 'تعديل الإشعارات',
            'notifications.delete' => 'حذف الإشعارات',

            // Settings Module
            'settings.view' => 'عرض الإعدادات',
            'settings.update' => 'تحديث الإعدادات',

            // Reports Module
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',

            // Roles & Permissions Management
            'roles.view' => 'عرض الأدوار',
            'roles.create' => 'إنشاء أدوار',
            'roles.update' => 'تعديل الأدوار',
            'roles.delete' => 'حذف الأدوار',
            'permissions.view' => 'عرض الصلاحيات',
        ];

        // Create all permissions with Arabic names
        foreach ($permissions as $permissionName => $arName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'ar_name' => $arName, 'guard_name' => 'web']
            );

            // Update existing permissions with Arabic names
            Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->update(['ar_name' => $arName]);
        }

        // Define roles with their Arabic names and permissions
        $roles = [
            'Admin' => [
                'ar_name' => 'مدير النظام',
                'permissions' => array_keys($permissions), // All permissions
            ],
            'Supplier' => [
                'ar_name' => 'مورد',
                'permissions' => [
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
                ],
            ],
            'Buyer' => [
                'ar_name' => 'مشتري',
                'permissions' => [
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
                ],
            ],
            'Staff' => [
                'ar_name' => 'موظف',
                'permissions' => [
                    // Baseline permissions for Staff (read-only access)
                    // Admin can grant additional permissions via UI
                    'users.view',
                    'suppliers.view',
                    'buyers.view',
                    'products.view',
                    'orders.view',
                    'rfqs.view',
                    'quotations.view',
                    'invoices.view',
                    'activity_logs.view',
                    'notifications.view',
                ],
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'ar_name' => $roleData['ar_name'], 'guard_name' => 'web']
            );

            // Update Arabic name if it exists
            $role->update(['ar_name' => $roleData['ar_name']]);

            // Sync permissions
            if (!empty($roleData['permissions'])) {
                $permissionObjects = Permission::whereIn('name', $roleData['permissions'])
                    ->where('guard_name', 'web')
                    ->get();
                $role->syncPermissions($permissionObjects);
            } else {
                $role->syncPermissions([]);
            }
        }

        $this->command->info('✅ Created ' . count($permissions) . ' permissions');
        $this->command->info('✅ Created/Updated ' . count($roles) . ' roles');
        $this->command->info('✅ Assigned permissions to roles');
    }
}

