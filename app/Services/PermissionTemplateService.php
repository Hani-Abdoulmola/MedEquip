<?php

namespace App\Services;

use App\Models\Permission;

/**
 * Permission Template Service
 * 
 * Provides pre-defined permission sets for common staff configurations
 */
class PermissionTemplateService
{
    /**
     * Get all available permission templates
     * 
     * @return array
     */
    public function getTemplates(): array
    {
        return [
            'read_only' => [
                'name' => 'قراءة فقط',
                'name_en' => 'Read Only',
                'description' => 'عرض البيانات فقط بدون تعديل',
                'description_en' => 'View data only, no modifications',
                'icon' => 'eye',
                'color' => 'blue',
                'permissions' => [
                    'users.view',
                    'suppliers.view',
                    'buyers.view',
                    'products.view',
                    'orders.view',
                    'rfqs.view',
                    'quotations.view',
                    'invoices.view',
                    'deliveries.view',
                    'activity_logs.view',
                    'notifications.view',
                    'reports.view',
                ],
            ],
            'product_manager' => [
                'name' => 'مدير المنتجات',
                'name_en' => 'Product Manager',
                'description' => 'إدارة كاملة للمنتجات والفئات',
                'description_en' => 'Full product and category management',
                'icon' => 'cube',
                'color' => 'green',
                'permissions' => [
                    'products.view',
                    'products.create',
                    'products.update',
                    'products.delete',
                    'products.approve',
                    'products.reject',
                    'products.request_changes',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                    'manufacturers.view',
                    'manufacturers.create',
                    'manufacturers.update',
                    'manufacturers.delete',
                ],
            ],
            'order_manager' => [
                'name' => 'مدير الطلبات',
                'name_en' => 'Order Manager',
                'description' => 'إدارة الطلبات والفواتير والتوصيل',
                'description_en' => 'Manage orders, invoices, and deliveries',
                'icon' => 'shopping-cart',
                'color' => 'purple',
                'permissions' => [
                    'orders.view',
                    'orders.create',
                    'orders.update',
                    'orders.confirm',
                    'orders.update_status',
                    'invoices.view',
                    'invoices.create',
                    'invoices.update',
                    'invoices.approve',
                    'invoices.download',
                    'deliveries.view',
                    'deliveries.create',
                    'deliveries.update',
                    'deliveries.update_status',
                    'deliveries.verify',
                ],
            ],
            'rfq_manager' => [
                'name' => 'مدير عروض الأسعار',
                'name_en' => 'RFQ Manager',
                'description' => 'إدارة طلبات عروض الأسعار والعروض',
                'description_en' => 'Manage RFQs and quotations',
                'icon' => 'file-text',
                'color' => 'yellow',
                'permissions' => [
                    'rfqs.view',
                    'rfqs.create',
                    'rfqs.update',
                    'rfqs.delete',
                    'rfqs.publish',
                    'rfqs.assign_suppliers',
                    'rfqs.update_status',
                    'quotations.view',
                    'quotations.compare',
                    'quotations.accept',
                    'quotations.reject',
                ],
            ],
            'financial_manager' => [
                'name' => 'مدير مالي',
                'name_en' => 'Financial Manager',
                'description' => 'إدارة الفواتير والمدفوعات والتقارير المالية',
                'description_en' => 'Manage invoices, payments, and financial reports',
                'icon' => 'dollar-sign',
                'color' => 'red',
                'permissions' => [
                    'invoices.view',
                    'invoices.create',
                    'invoices.update',
                    'invoices.approve',
                    'invoices.download',
                    'invoices.export',
                    'payments.view',
                    'payments.create',
                    'payments.update',
                    'payments.export',
                    'reports.view',
                    'reports.export',
                    'orders.view',
                ],
            ],
            'user_manager' => [
                'name' => 'مدير المستخدمين',
                'name_en' => 'User Manager',
                'description' => 'إدارة المستخدمين والموردين والمشترين',
                'description_en' => 'Manage users, suppliers, and buyers',
                'icon' => 'users',
                'color' => 'indigo',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.update',
                    'suppliers.verify',
                    'suppliers.toggle_active',
                    'buyers.view',
                    'buyers.create',
                    'buyers.update',
                    'buyers.verify',
                    'buyers.toggle_active',
                ],
            ],
            'full_access' => [
                'name' => 'صلاحيات كاملة',
                'name_en' => 'Full Access',
                'description' => 'جميع الصلاحيات (مثل Admin)',
                'description_en' => 'All permissions (Admin-like)',
                'icon' => 'shield',
                'color' => 'gray',
                'permissions' => 'all', // Special marker for all permissions
            ],
        ];
    }

    /**
     * Get permission IDs for a template
     * 
     * @param string $templateKey
     * @return array
     */
    public function getTemplatePermissionIds(string $templateKey): array
    {
        $templates = $this->getTemplates();
        
        if (!isset($templates[$templateKey])) {
            return [];
        }
        
        $template = $templates[$templateKey];
        
        // Handle 'all' permissions case
        if ($template['permissions'] === 'all') {
            $adminPermissionService = app(AdminPermissionService::class);
            return $adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
        }
        
        // Get permission IDs from names
        return Permission::whereIn('name', $template['permissions'])
            ->where('guard_name', 'web')
            ->pluck('id')
            ->toArray();
    }

    /**
     * Apply template to user
     * 
     * @param \App\Models\User $user
     * @param string $templateKey
     * @param bool $merge If true, merge with existing permissions. If false, replace.
     * @return int Number of permissions assigned
     */
    public function applyTemplateToUser($user, string $templateKey, bool $merge = false): int
    {
        $permissionIds = $this->getTemplatePermissionIds($templateKey);
        
        if (empty($permissionIds)) {
            return 0;
        }
        
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        
        if ($merge) {
            // Merge: Add new permissions without removing existing ones
            $user->givePermissionTo($permissions);
        } else {
            // Replace: Sync (remove old, add new)
            $user->syncPermissions($permissions);
        }
        
        return $permissions->count();
    }

    /**
     * Get template that best matches user's current permissions
     * 
     * @param \App\Models\User $user
     * @return string|null Template key or null
     */
    public function detectUserTemplate($user): ?string
    {
        $userPermissionNames = $user->permissions->pluck('name')->toArray();
        
        if (empty($userPermissionNames)) {
            return null;
        }
        
        $bestMatch = null;
        $highestMatchPercentage = 0;
        
        foreach ($this->getTemplates() as $key => $template) {
            if ($template['permissions'] === 'all') {
                continue; // Skip full access template
            }
            
            $templatePermissions = $template['permissions'];
            $matchingCount = count(array_intersect($userPermissionNames, $templatePermissions));
            $matchPercentage = ($matchingCount / count($templatePermissions)) * 100;
            
            if ($matchPercentage > $highestMatchPercentage && $matchPercentage >= 80) {
                $highestMatchPercentage = $matchPercentage;
                $bestMatch = $key;
            }
        }
        
        return $bestMatch;
    }
}
