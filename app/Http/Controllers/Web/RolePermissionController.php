<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Services\AdminPermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    protected AdminPermissionService $adminPermissionService;

    public function __construct(AdminPermissionService $adminPermissionService)
    {
        $this->adminPermissionService = $adminPermissionService;
    }

    /**
     * Display unified Roles & Permissions management page
     * Shows user selector and permissions table
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\Permission::class);

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

        // Get all permissions grouped by module (only admin permissions)
        $permissions = $this->adminPermissionService->getAdminPermissions()
            ->groupBy(function ($permission) {
                // Extract module from permission name (e.g., 'users.view' -> 'users')
                return explode('.', $permission->name)[0];
            });

        // Module labels in Arabic
        $moduleLabels = [
            'users' => 'المستخدمون',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
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
        ];

        return view('admin.role-permissions.index', compact(
            'users',
            'selectedUser',
            'permissions',
            'userPermissions',
            'moduleLabels'
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
            // Get admin permissions only (validate)
            $adminPermissionIds = $this->adminPermissionService->getAdminPermissions()->pluck('id')->toArray();
            $requestedPermissionIds = $request->permissions ?? [];

            // Filter to ensure only admin permissions are assigned
            $validPermissionIds = array_intersect($requestedPermissionIds, $adminPermissionIds);

            // Get permission models
            $permissions = Permission::whereIn('id', $validPermissionIds)->get();

            // Sync ONLY direct permissions (this replaces all existing direct permissions)
            $user->syncPermissions($permissions);

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
                ->route('admin.role-permissions.index', ['user_id' => $user->id])
                ->with('success', '✅ تم تحديث صلاحيات المستخدم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Permission assignment error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()]);
        }
    }
}

