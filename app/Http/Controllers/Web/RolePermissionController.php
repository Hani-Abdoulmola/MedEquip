<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Services\AdminPermissionService;
use App\Services\PermissionAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    protected AdminPermissionService $adminPermissionService;
    protected PermissionAuditService $auditService;

    public function __construct(
        AdminPermissionService $adminPermissionService,
        PermissionAuditService $auditService
    ) {
        $this->adminPermissionService = $adminPermissionService;
        $this->auditService = $auditService;
    }

    /**
     * Display unified Roles & Permissions management page
     * Shows user selector and permissions table
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\Permission::class);

        // Determine active tab (users or roles)
        $activeTab = $request->get('tab', 'users');

        // Get internal users only (Staff/Admin types, exclude Supplier/Buyer)
        $users = User::whereHas('type', function ($query) {
            $query->whereIn('name', ['مدير النظام', 'موظف']);
        })
        ->orWhereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Staff']);
        })
        ->with(['roles', 'permissions'])
        ->orderBy('name')
        ->get();

        // Get internal roles only (exclude Supplier/Buyer roles)
        $roles = \App\Models\Role::whereIn('name', ['Admin', 'Staff'])
            ->with('permissions')
            ->get();

        // Get selected user from request
        $selectedUser = null;
        $userPermissions = [];

        if ($request->filled('user_id')) {
            $selectedUser = User::with(['roles', 'permissions'])->find($request->user_id);
            if ($selectedUser) {
                // Get only direct permissions (not role permissions)
                $userPermissions = $selectedUser->permissions->pluck('id')->toArray();
            }
        }

        // Get selected role from request
        $selectedRole = null;
        $rolePermissions = [];

        if ($request->filled('role_id')) {
            $selectedRole = \App\Models\Role::with('permissions')->find($request->role_id);
            if ($selectedRole) {
                $rolePermissions = $selectedRole->permissions->pluck('id')->toArray();
            }
        }

        // Get all permissions grouped by module (only admin permissions)
        $permissions = $this->adminPermissionService->getAdminPermissions()
            ->groupBy(function ($permission) {
                // Extract module from permission name (e.g., 'users.view' -> 'users')
                return explode('.', $permission->name)[0];
            });

        // Module labels in Arabic (matching all permissions now enforced in routes)
        $moduleLabels = [
            'users' => 'المستخدمون',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
            'suppliers' => 'الموردين',
            'buyers' => 'المشترين',
            'products' => 'المنتجات',
            'orders' => 'الطلبات',
            'invoices' => 'الفواتير',
            'payments' => 'المدفوعات',
            'deliveries' => 'التوصيل',
            'rfqs' => 'طلبات عروض الأسعار',
            'quotations' => 'عروض الأسعار',
            'categories' => 'الفئات',
            'manufacturers' => 'الشركات المصنعة',
            'settings' => 'الإعدادات',
            'activity_logs' => 'سجل النشاط',
            'notifications' => 'الإشعارات',
            'reports' => 'التقارير',
        ];

        return view('admin.role-permissions.index', compact(
            'users',
            'roles',
            'selectedUser',
            'selectedRole',
            'permissions',
            'userPermissions',
            'rolePermissions',
            'moduleLabels',
            'activeTab'
        ));
    }

    /**
     * Assign permissions to a user
     * Only assigns direct permissions (no role inheritance)
     */
    public function assignPermissions(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();

        try {
            // Get old permissions for audit
            $oldPermissions = $user->permissions;
            
            // Get admin permissions only (validate)
            $adminPermissionIds = $this->adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
            $requestedPermissionIds = $request->permissions ?? [];

            // Filter to ensure only admin permissions are assigned
            $validPermissionIds = array_intersect($requestedPermissionIds, $adminPermissionIds);

            // Get permission models
            $permissions = Permission::whereIn('id', $validPermissionIds)->get();

            // Sync ONLY direct permissions (this replaces all existing direct permissions)
            $user->syncPermissions($permissions);

            // Log to permission audit
            $this->auditService->logUserPermissionChange(
                $user,
                $oldPermissions->toArray(),
                $permissions->toArray(),
                'synced'
            );

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'permissions_count' => $permissions->count(),
                    'permission_names' => $permissions->pluck('name')->toArray(),
                ])
                ->log('🔐 تم تحديث صلاحيات المستخدم');

            DB::commit();

            return redirect()
                ->route('admin.role-permissions.index', ['user_id' => $user->id, 'tab' => 'users'])
                ->with('success', '✅ تم تحديث صلاحيات المستخدم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Permission assignment error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()]);
        }
    }

    /**
     * Update permissions for a role
     * This affects all users with this role
     */
    public function updateRolePermissions(Request $request, \App\Models\Role $role): RedirectResponse
    {
        // Prevent modifying Supplier/Buyer roles
        if (in_array($role->name, ['Supplier', 'Buyer'])) {
            return back()->withErrors(['error' => 'لا يمكن تعديل صلاحيات أدوار الموردين والمشترين']);
        }

        $this->authorize('viewAny', \App\Models\Permission::class);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();

        try {
            // Get old permissions for audit
            $oldPermissions = $role->permissions;
            
            // Get admin permissions only (validate)
            $adminPermissionIds = $this->adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
            $requestedPermissionIds = $request->permissions ?? [];

            // Filter to ensure only admin permissions are assigned
            $validPermissionIds = array_intersect($requestedPermissionIds, $adminPermissionIds);

            // Get permission models
            $permissions = Permission::whereIn('id', $validPermissionIds)->get();

            // Sync role permissions (affects all users with this role)
            $role->syncPermissions($permissions);

            // Log to permission audit
            $this->auditService->logRolePermissionChange(
                $role,
                $oldPermissions->toArray(),
                $permissions->toArray()
            );

            // Log activity
            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties([
                    'role_name' => $role->name,
                    'permissions_count' => $permissions->count(),
                    'permission_names' => $permissions->pluck('name')->toArray(),
                ])
                ->log('🔐 تم تحديث صلاحيات الدور');

            DB::commit();

            return redirect()
                ->route('admin.role-permissions.index', ['role_id' => $role->id, 'tab' => 'roles'])
                ->with('success', "✅ تم تحديث صلاحيات دور {$role->ar_name} بنجاح. جميع المستخدمين بهذا الدور سيرثون هذه الصلاحيات.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role permission update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()]);
        }
    }


    /**
     * Bulk assign permissions to multiple users
     */
    public function bulkAssignPermissions(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', \App\Models\Permission::class);

        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
            'action' => ['required', 'in:replace,merge,remove'],
        ]);

        DB::beginTransaction();

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $requestedPermissionIds = $request->permissions ?? [];

            // Filter admin permissions only
            $adminPermissionIds = $this->adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
            $validPermissionIds = array_intersect($requestedPermissionIds, $adminPermissionIds);
            $permissions = Permission::whereIn('id', $validPermissionIds)->get();

            $affectedCount = 0;

            foreach ($users as $user) {
                // Check authorization for each user
                if (!auth()->user()->can('update', $user)) {
                    continue;
                }

                switch ($request->action) {
                    case 'replace':
                        $user->syncPermissions($permissions);
                        break;
                    case 'merge':
                        $user->givePermissionTo($permissions);
                        break;
                    case 'remove':
                        $user->revokePermissionTo($permissions);
                        break;
                }

                $affectedCount++;
            }

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => $request->action,
                    'user_count' => $affectedCount,
                    'permissions_count' => $permissions->count(),
                ])
                ->log('👥 تم تحديث صلاحيات عدة مستخدمين دفعة واحدة');

            DB::commit();

            $actionText = match($request->action) {
                'replace' => 'استبدال',
                'merge' => 'إضافة',
                'remove' => 'إزالة',
            };

            return redirect()
                ->route('admin.role-permissions.index', ['tab' => 'users'])
                ->with('success', "✅ تم {$actionText} صلاحيات {$affectedCount} مستخدم بنجاح");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bulk permission assignment error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء التحديث الجماعي: ' . $e->getMessage()]);
        }
    }

}

