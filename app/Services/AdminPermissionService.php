<?php

namespace App\Services;

use App\Models\Permission;

/**
 * Service for managing admin/system permissions.
 * 
 * This service provides methods to:
 * - Get admin-only permissions (excluding supplier/buyer permissions)
 * - Validate that only admin permissions are assigned to staff/internal users
 */
class AdminPermissionService
{
    /**
     * Get admin/system management permissions only.
     * 
     * Excludes supplier and buyer related permissions.
     * These permissions are for internal staff/admin users only.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAdminPermissions()
    {
        // List of supplier/buyer related permissions to exclude
        $excludedPermissions = [
            // Dot notation
            'suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'suppliers.verify', 'suppliers.toggle_active',
            'buyers.view', 'buyers.create', 'buyers.update', 'buyers.delete',
            'buyers.verify', 'buyers.toggle_active',
            // RFQ permission that assigns suppliers
            'rfqs.assign_suppliers',
        ];

        return Permission::where(function ($query) use ($excludedPermissions) {
            // Only include dot-notation permissions (e.g., users.view, not "view users")
            $query->where('name', 'like', '%.%')
                // Exclude permissions that contain 'supplier' or 'buyer' in the name
                ->where('name', 'not like', 'suppliers.%')
                ->where('name', 'not like', 'buyers.%')
                ->where('name', 'not like', '%supplier%')
                ->where('name', 'not like', '%buyer%');
        })
        ->whereNotIn('name', $excludedPermissions)
        ->orderBy('name')
        ->get();
    }

    /**
     * Validate that permissions don't include supplier/buyer permissions.
     * 
     * @param array $permissionIds
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateAdminPermissionsOnly(array $permissionIds): void
    {
        if (empty($permissionIds)) {
            return;
        }

        // Get all admin permission IDs
        $adminPermissionIds = $this->getAdminPermissions()->pluck('id')->toArray();

        // Find any permission IDs that are not in the admin list
        $forbiddenPermissionIds = array_diff($permissionIds, $adminPermissionIds);

        if (!empty($forbiddenPermissionIds)) {
            $forbiddenPermissions = Permission::whereIn('id', $forbiddenPermissionIds)
                ->pluck('name')
                ->toArray();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'permissions' => 'لا يمكن تعيين صلاحيات الموردين أو المشترين للموظفين. الصلاحيات المسموحة هي فقط وظائف إدارة النظام. الصلاحيات المرفوضة: ' . implode(', ', $forbiddenPermissions),
            ]);
        }
    }

    /**
     * Check if a permission is an admin-only permission.
     * 
     * @param string $permissionName
     * @return bool
     */
    public function isAdminPermission(string $permissionName): bool
    {
        $adminPermissions = $this->getAdminPermissions()->pluck('name')->toArray();
        return in_array($permissionName, $adminPermissions);
    }
}

