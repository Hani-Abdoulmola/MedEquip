<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // الصلاحيات الأساسية مع الأسماء العربية (باستخدام الترميز بالنقاط)
        $permissions = [
            // Users
            'users.view' => 'عرض المستخدمين',
            'users.create' => 'إنشاء مستخدمين',
            'users.update' => 'تعديل المستخدمين',
            'users.delete' => 'حذف المستخدمين',
            'users.manage_permissions' => 'إدارة صلاحيات المستخدمين',

            // Suppliers
            'suppliers.view' => 'عرض الموردين',
            'suppliers.create' => 'إنشاء موردين',
            'suppliers.update' => 'تعديل الموردين',
            'suppliers.delete' => 'حذف الموردين',
            'suppliers.verify' => 'التحقق من الموردين',
            'suppliers.toggle_active' => 'تفعيل/تعطيل الموردين',

            // Buyers
            'buyers.view' => 'عرض المشترين',
            'buyers.create' => 'إنشاء مشترين',
            'buyers.update' => 'تعديل المشترين',
            'buyers.delete' => 'حذف المشترين',
            'buyers.verify' => 'التحقق من المشترين',
            'buyers.toggle_active' => 'تفعيل/تعطيل المشترين',

            // RFQs (Request for Quotations)
            'rfqs.view' => 'عرض طلبات عروض الأسعار',
            'rfqs.create' => 'إنشاء طلبات عروض أسعار',
            'rfqs.update' => 'تعديل طلبات عروض الأسعار',
            'rfqs.delete' => 'حذف طلبات عروض الأسعار',
            'rfqs.publish' => 'نشر طلبات عروض الأسعار',
            'rfqs.assign_suppliers' => 'تعيين موردين لطلبات عروض الأسعار',
            'rfqs.update_status' => 'تحديث حالة طلبات عروض الأسعار',
            'rfqs.toggle_visibility' => 'إظهار/إخفاء طلبات عروض الأسعار',

            // Quotations
            'quotations.view' => 'عرض عروض الأسعار',
            'quotations.submit' => 'تقديم عروض أسعار',
            'quotations.update' => 'تعديل عروض الأسعار',
            'quotations.delete' => 'حذف عروض الأسعار',
            'quotations.accept' => 'قبول عروض الأسعار',
            'quotations.reject' => 'رفض عروض الأسعار',
            'quotations.compare' => 'مقارنة عروض الأسعار',

            // Orders
            'orders.view' => 'عرض الطلبات',
            'orders.create' => 'إنشاء طلبات',
            'orders.update' => 'تعديل الطلبات',
            'orders.delete' => 'حذف الطلبات',
            'orders.confirm' => 'تأكيد الطلبات',
            'orders.update_status' => 'تحديث حالة الطلبات',

            // Invoices
            'invoices.view' => 'عرض الفواتير',
            'invoices.create' => 'إنشاء فواتير',
            'invoices.update' => 'تعديل الفواتير',
            'invoices.delete' => 'حذف الفواتير',
            'invoices.approve' => 'الموافقة على الفواتير',
            'invoices.download' => 'تحميل الفواتير',
            'invoices.export' => 'تصدير الفواتير',

            // Payments
            'payments.view' => 'عرض المدفوعات',
            'payments.create' => 'إنشاء مدفوعات',
            'payments.update' => 'تعديل المدفوعات',
            'payments.delete' => 'حذف المدفوعات',
            'payments.export' => 'تصدير المدفوعات',

            // Deliveries
            'deliveries.view' => 'عرض عمليات التسليم',
            'deliveries.create' => 'إنشاء عمليات تسليم',
            'deliveries.update' => 'تعديل عمليات التسليم',
            'deliveries.delete' => 'حذف عمليات التسليم',
            'deliveries.update_status' => 'تحديث حالة عمليات التسليم',
            'deliveries.verify' => 'التحقق من عمليات التسليم',
            'deliveries.upload_proof' => 'رفع إثبات التسليم',

            // Products
            'products.view' => 'عرض المنتجات',
            'products.create' => 'إنشاء منتجات',
            'products.update' => 'تعديل المنتجات',
            'products.delete' => 'حذف المنتجات',
            'products.approve' => 'الموافقة على المنتجات',
            'products.reject' => 'رفض المنتجات',
            'products.request_changes' => 'طلب تعديلات على المنتجات',

            // Manufacturers
            'manufacturers.view' => 'عرض الشركات المصنعة',
            'manufacturers.create' => 'إنشاء شركات مصنعة',
            'manufacturers.update' => 'تعديل الشركات المصنعة',
            'manufacturers.delete' => 'حذف الشركات المصنعة',

            // Categories
            'categories.view' => 'عرض الفئات',
            'categories.create' => 'إنشاء فئات',
            'categories.update' => 'تعديل الفئات',
            'categories.delete' => 'حذف الفئات',

            // Activity Logs
            'activity_logs.view' => 'عرض سجل النشاط',
            'activity_logs.delete' => 'حذف سجل النشاط',

            // Notifications
            'notifications.view' => 'عرض الإشعارات',
            'notifications.create' => 'إنشاء إشعارات',
            'notifications.update' => 'تعديل الإشعارات',
            'notifications.delete' => 'حذف الإشعارات',

            // Settings
            'settings.view' => 'عرض الإعدادات',
            'settings.update' => 'تحديث الإعدادات',

            // Reports
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',

            // Roles & Permissions (Admin only)
            'roles.view' => 'عرض الأدوار',
            'roles.create' => 'إنشاء أدوار',
            'roles.update' => 'تعديل الأدوار',
            'roles.delete' => 'حذف الأدوار',
            'permissions.view' => 'عرض الصلاحيات',
        ];

        foreach ($permissions as $permission => $arName) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['ar_name' => $arName]
            );

            // Update existing permissions with Arabic names
            Permission::where('name', $permission)
                ->where('guard_name', 'web')
                ->update(['ar_name' => $arName]);
        }

        // الأدوار الأساسية
        $roles = [
            'Admin' => array_keys($permissions), // كل الصلاحيات (استخدام المفاتيح = الأسماء الإنجليزية)
            'Supplier' => [
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
            'Buyer' => [
                'users.view',
                'suppliers.view',
                'products.view',
                'orders.view',
                'orders.create',
                'activity_logs.view',
                'rfqs.view',
                'rfqs.create',
                'rfqs.update',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
