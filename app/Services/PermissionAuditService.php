<?php

namespace App\Services;

use App\Models\PermissionAudit;
use App\Models\User;
use App\Models\Role;

/**
 * Permission Audit Service
 * 
 * Handles permission change auditing and reporting
 */
class PermissionAuditService
{
    /**
     * Log user permission change
     */
    public function logUserPermissionChange(
        User $user,
        array $oldPermissions,
        array $newPermissions,
        string $action = 'synced',
        ?string $templateName = null
    ): PermissionAudit {
        $oldNames = collect($oldPermissions)->pluck('name')->toArray();
        $newNames = collect($newPermissions)->pluck('name')->toArray();
        
        $added = array_values(array_diff($newNames, $oldNames));
        $removed = array_values(array_diff($oldNames, $newNames));
        
        return PermissionAudit::logPermissionChange(
            action: $action,
            entityType: 'user',
            entityId: $user->id,
            entityName: $user->name,
            permissionsAdded: $added,
            permissionsRemoved: $removed,
            templateName: $templateName
        );
    }

    /**
     * Log role permission change
     */
    public function logRolePermissionChange(
        Role $role,
        array $oldPermissions,
        array $newPermissions
    ): PermissionAudit {
        $oldNames = collect($oldPermissions)->pluck('name')->toArray();
        $newNames = collect($newPermissions)->pluck('name')->toArray();
        
        $added = array_values(array_diff($newNames, $oldNames));
        $removed = array_values(array_diff($oldNames, $newNames));
        
        // Count affected users
        $affectedUsersCount = User::role($role->name)->count();
        
        return PermissionAudit::logPermissionChange(
            action: 'role_updated',
            entityType: 'role',
            entityId: $role->id,
            entityName: $role->ar_name ?? $role->name,
            permissionsAdded: $added,
            permissionsRemoved: $removed,
            metadata: [
                'affected_users_count' => $affectedUsersCount,
                'role_name' => $role->name,
            ]
        );
    }

    /**
     * Get recent audit logs
     */
    public function getRecentLogs(int $limit = 50)
    {
        return PermissionAudit::with('adminUser')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for specific user
     */
    public function getUserLogs(int $userId, int $limit = 20)
    {
        return PermissionAudit::where('entity_type', 'user')
            ->where('entity_id', $userId)
            ->with('adminUser')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for specific role
     */
    public function getRoleLogs(int $roleId, int $limit = 20)
    {
        return PermissionAudit::where('entity_type', 'role')
            ->where('entity_id', $roleId)
            ->with('adminUser')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs by admin user
     */
    public function getLogsByAdmin(int $adminUserId, int $limit = 50)
    {
        return PermissionAudit::where('admin_user_id', $adminUserId)
            ->with('adminUser')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit statistics
     */
    public function getStatistics(int $days = 30): array
    {
        $since = now()->subDays($days);
        
        $totalChanges = PermissionAudit::where('created_at', '>=', $since)->count();
        $userChanges = PermissionAudit::where('entity_type', 'user')
            ->where('created_at', '>=', $since)
            ->count();
        $roleChanges = PermissionAudit::where('entity_type', 'role')
            ->where('created_at', '>=', $since)
            ->count();
        
        $topAdmins = PermissionAudit::where('created_at', '>=', $since)
            ->groupBy('admin_user_id')
            ->selectRaw('admin_user_id, COUNT(*) as changes_count')
            ->orderByDesc('changes_count')
            ->limit(5)
            ->with('adminUser')
            ->get();
        
        $actionBreakdown = PermissionAudit::where('created_at', '>=', $since)
            ->groupBy('action')
            ->selectRaw('action, COUNT(*) as count')
            ->get()
            ->pluck('count', 'action')
            ->toArray();
        
        return [
            'total_changes' => $totalChanges,
            'user_changes' => $userChanges,
            'role_changes' => $roleChanges,
            'top_admins' => $topAdmins,
            'action_breakdown' => $actionBreakdown,
            'period_days' => $days,
        ];
    }
}
