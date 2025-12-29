<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    /**
     * Get admin/system management permissions only (exclude suppliers and buyers).
     * Employees (Staff) should only have access to admin functions, not supplier/buyer management.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAdminPermissions()
    {
        // List of all supplier/buyer related permissions to exclude
        $excludedPermissions = [
            // Dot notation
            'suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'suppliers.verify', 'suppliers.toggle_active',
            'buyers.view', 'buyers.create', 'buyers.update', 'buyers.delete',
            'buyers.verify', 'buyers.toggle_active',
            // Space notation (legacy)
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view buyers', 'create buyers', 'edit buyers', 'delete buyers',
            // RFQ permission that assigns suppliers
            'rfqs.assign_suppliers',
        ];

        return Permission::where(function ($query) use ($excludedPermissions) {
            // Exclude permissions that contain 'supplier' or 'buyer' in the name
            $query->where('name', 'not like', 'suppliers.%')
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
    private function validateAdminPermissionsOnly(array $permissionIds): void
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
     * Display a listing of roles.
     */
    public function index(): View
    {
        // Direct role check since policy resolution has issues
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        // Get only custom roles (exclude system roles: Admin, Supplier, Buyer, Staff)
        $systemRoles = ['Admin', 'Supplier', 'Buyer', 'Staff'];
        $roles = Role::with('permissions')
            ->whereNotIn('name', $systemRoles)
            ->withCount('users')
            ->latest('id')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        // Only show admin/system management permissions (exclude suppliers and buyers)
        $permissions = $this->getAdminPermissions()->groupBy(function ($permission) {
            // Group by module (e.g., 'users.view' -> 'users')
            return explode('.', $permission->name)[0];
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'ar_name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Validate that only admin/system management permissions are assigned
        if (!empty($validated['permissions'])) {
            try {
                $this->validateAdminPermissionsOnly($validated['permissions']);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()
                    ->withInput()
                    ->withErrors($e->errors());
            }
        }

        DB::beginTransaction();

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'ar_name' => $validated['ar_name'],
                'guard_name' => 'web',
            ]);

            if (!empty($validated['permissions'])) {
                // Double-check: only get admin permissions
                $adminPermissionIds = $this->getAdminPermissions()->pluck('id')->toArray();
                $allowedPermissionIds = array_intersect($validated['permissions'], $adminPermissionIds);
                $permissions = Permission::whereIn('id', $allowedPermissionIds)->get();
                $role->syncPermissions($permissions);
            }

            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties(['name' => $role->name])
                ->log('تم إنشاء دور جديد');

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم إنشاء الدور بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role store error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $role->load(['permissions', 'users']);
        // Only show admin/system management permissions
        $allPermissions = $this->getAdminPermissions()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.roles.show', compact('role', 'allPermissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        // Only show admin/system management permissions (exclude suppliers and buyers)
        $permissions = $this->getAdminPermissions()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'ar_name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Validate that only admin/system management permissions are assigned
        if (isset($validated['permissions']) && !empty($validated['permissions'])) {
            try {
                $this->validateAdminPermissionsOnly($validated['permissions']);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()
                    ->withInput()
                    ->withErrors($e->errors());
            }
        }

        DB::beginTransaction();

        try {
            $role->update([
                'name' => $validated['name'],
                'ar_name' => $validated['ar_name'],
            ]);

            if (isset($validated['permissions'])) {
                // Double-check: only get admin permissions
                $adminPermissionIds = $this->getAdminPermissions()->pluck('id')->toArray();
                $allowedPermissionIds = array_intersect($validated['permissions'], $adminPermissionIds);
                $permissions = Permission::whereIn('id', $allowedPermissionIds)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties(['name' => $role->name])
                ->log('تم تحديث الدور');

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم تحديث الدور بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        // Prevent deletion of system roles
        $systemRoles = ['Admin', 'Supplier', 'Buyer', 'Staff'];
        if (in_array($role->name, $systemRoles)) {
            return back()
                ->withErrors(['error' => 'لا يمكن حذف الأدوار النظامية.']);
        }

        // Prevent deletion if role has users assigned
        if ($role->users()->count() > 0) {
            return back()
                ->withErrors(['error' => 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين. يرجى إزالة المستخدمين أولاً.']);
        }

        try {
            $roleName = $role->name;
            $role->delete();

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['name' => $roleName])
                ->log('تم حذف الدور');

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم حذف الدور بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Role delete error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage()]);
        }
    }
}

