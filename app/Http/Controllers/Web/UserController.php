<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use App\Exports\AdminUsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Role;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🧾 عرض المستخدمين الإداريين فقط (Admin/Staff)
     * الموردين والمشترين يتم إدارتهم من أقسام منفصلة
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        // تصفية فقط المستخدمين الإداريين (Admin user_type_id=1 و Staff user_type_id=4)
        // Admin (user_type_id = 1) and Staff (user_type_id = 4)
        $query = User::with(['type', 'creator', 'updater', 'roles'])
            ->whereIn('user_type_id', [1, 4]); // Admin (1) and Staff (4) user types

        // 🔍 Search by name or email
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🔍 Filter by role
        if (request()->filled('role')) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', request('role'));
            });
        }

        // 🔍 Filter by status
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $users = $query->latest('id')->paginate(15);

        // 📊 Calculate stats (فقط للمستخدمين الإداريين)
        // Admin (user_type_id = 1) and Staff (user_type_id = 4)
        $stats = [
            'total_users' => User::whereIn('user_type_id', [1, 4])
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'Staff']))
                ->count(),
            'active_users' => User::whereIn('user_type_id', [1, 4])
                ->where('status', 'active')
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'Staff']))
                ->count(),
            'admin_count' => User::where('user_type_id', 1)
                ->whereHas('roles', fn($q) => $q->where('name', 'Admin'))
                ->count(),
            'staff_count' => User::where('user_type_id', 4)
                ->whereHas('roles', fn($q) => $q->where('name', 'Staff'))
                ->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * ➕ صفحة إنشاء مستخدم جديد
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        // إدارة المستخدمين مقتصرة فقط على الموظفين الإداريين (Admin/Staff)
        // الموردين والمشترين يتم إدارتهم من أقسام منفصلة

        // Get Admin (user_type_id = 1) and Staff (user_type_id = 4) user types
        $adminType = UserType::find(1); // Admin user_type_id = 1
        $staffType = UserType::find(4); // Staff user_type_id = 4

        if (!$adminType) {
            abort(500, 'نوع المستخدم الإداري غير موجود');
        }

        // Get only Admin and Staff roles
        $allRoles = Role::where('guard_name', 'web')
            ->whereIn('name', ['Admin', 'Staff'])
            ->get()
            ->keyBy('name');

        // Create combined options: user_type_id:role_name => "User Type - Role"
        $combinedOptions = [];

        // Admin type can have Admin role
        $adminRole = $allRoles->get('Admin');
        if ($adminRole && $adminType) {
            $roleArName = $adminRole->ar_name ?? $adminRole->name;
            $key = "{$adminType->id}:Admin";
            $adminDescription = $adminType->description ?? 'مدير النظام';
            $combinedOptions[$key] = "{$adminDescription} - {$roleArName}";
        }

        // Staff type can have Staff role (if Staff type exists)
        // Otherwise, use Admin type with Staff role (backward compatibility)
        $staffRole = $allRoles->get('Staff');
        if ($staffRole) {
            $roleArName = $staffRole->ar_name ?? $staffRole->name;

            if ($staffType) {
                // Use Staff user type if it exists
                $key = "{$staffType->id}:Staff";
                $staffDescription = $staffType->description ?? 'موظف إداري';
                $combinedOptions[$key] = "{$staffDescription} - {$roleArName}";
            } else {
                // Fallback: Use Admin type with Staff role (backward compatibility)
                $key = "{$adminType->id}:Staff";
                $adminDescription = $adminType->description ?? 'مدير النظام';
                $combinedOptions[$key] = "{$adminDescription} - {$roleArName}";
            }
        }

        return view('admin.users.create', [
            'combinedOptions' => $combinedOptions,
            'user' => new User,
        ]);
    }

    /**
     * 💾 حفظ مستخدم جديد
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['created_by'] = Auth::id();

            // Extract user_type_id and role from combined field
            $userTypeId = null;
            $roleName = null;

            if ($request->filled('user_type_role')) {
                $userTypeRoleValue = trim($request->user_type_role);
                
                // Check if format is "user_type_id:role_name" (e.g., "1:Admin")
                if (strpos($userTypeRoleValue, ':') !== false) {
                    $parts = explode(':', $userTypeRoleValue);
                    if (count($parts) === 2) {
                        $userTypeId = (int) $parts[0];
                        $roleName = trim($parts[1]);
                        $data['user_type_id'] = $userTypeId;
                    }
                } else {
                    // Format is just "role_name" (e.g., "Admin" or "Staff")
                    $roleName = $userTypeRoleValue;
                    
                    // Infer user_type_id based on role name
                    if (strtolower($roleName) === 'admin') {
                        $userTypeId = 1; // Admin user_type_id
                        $data['user_type_id'] = $userTypeId;
                    } elseif (strtolower($roleName) === 'staff') {
                        $staffType = UserType::find(4); // Staff user_type_id = 4
                        $userTypeId = $staffType ? 4 : 1; // Fallback to Admin if Staff type doesn't exist
                        $data['user_type_id'] = $userTypeId;
                    } else {
                        // Unknown role, default to Admin type
                        $userTypeId = 1;
                        $data['user_type_id'] = $userTypeId;
                    }
                }
            } elseif ($request->filled('user_type_id')) {
                // Fallback: if separate fields exist
                $userTypeId = $request->user_type_id;
                $data['user_type_id'] = $userTypeId;
                $roleName = $request->role;
            }

            $user = User::create($data);

            // 🧩 تعيين الدور (Role grants permissions automatically via Spatie)
            if ($roleName) {
                $user->assignRole($roleName);
                // User now inherits all permissions from assigned role
                // Additional permissions can be granted via "الأدوار و الصلاحيات" page
            }

            // ✅ Email verification
            if ($request->filled('email_verified') && $request->email_verified) {
                $user->email_verified_at = now();
                $user->save();
            }

            // 📧 Send welcome email if requested
            if ($request->filled('send_welcome_email') && $request->send_welcome_email) {
                try {
                    NotificationService::send(
                        $user,
                        '🎉 مرحباً بك في MediEquip',
                        "مرحباً {$user->name}! تم إنشاء حسابك بنجاح. يمكنك تسجيل الدخول الآن باستخدام البريد الإلكتروني وكلمة المرور الخاصة بك.",
                        route('login')
                    );
                } catch (\Throwable $e) {
                    Log::warning('Failed to send welcome email: '.$e->getMessage());
                }
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'email' => $user->email,
                    'user_type_id' => $userTypeId,
                    'role' => $roleName ?? 'غير محدد',
                ])
                ->log('👤 تم إنشاء مستخدم جديد');

            // 🔔 إشعار المديرين
            NotificationService::notifyAdmins(
                '👤 مستخدم جديد أُضيف للنظام',
                "تم إنشاء حساب جديد باسم {$user->name} (البريد: {$user->email}).",
                route('admin.users')
            );

            // 🔔 إشعار المستخدم نفسه
            NotificationService::send(
                $user,
                '🎉 تم إنشاء حسابك في النظام',
                'مرحبًا بك! تم إنشاء حسابك بنجاح. يمكنك تسجيل الدخول الآن باستخدام البريد وكلمة المرور الخاصة بك.',
                route('login')
            );

            DB::commit();

            return redirect()
                ->route('admin.users')
                ->with('success', '✅ تم إنشاء المستخدم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء المستخدم: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ صفحة تعديل مستخدم
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        // إدارة المستخدمين مقتصرة فقط على الموظفين الإداريين
        // التأكد من أن المستخدم المراد تعديله هو موظف إداري
        // Admin (user_type_id = 1) and Staff (user_type_id = 4)
        if (!in_array($user->user_type_id, [1, 4])) {
            abort(403, 'يمكن تعديل الموظفين الإداريين فقط من هذه الصفحة');
        }

        // Get Admin (user_type_id = 1) and Staff (user_type_id = 4) user types
        $adminType = UserType::find(1); // Admin user_type_id = 1
        $staffType = UserType::find(4); // Staff user_type_id = 4

        if (!$adminType) {
            abort(500, 'نوع المستخدم الإداري غير موجود');
        }

        // Get only Admin and Staff roles
        $allRoles = Role::where('guard_name', 'web')
            ->whereIn('name', ['Admin', 'Staff'])
            ->get()
            ->keyBy('name');

        // Create combined options for current user
        $combinedOptions = [];

        // Admin type (user_type_id = 1) can have Admin role
        $adminRole = $allRoles->get('Admin');
        if ($adminRole && $adminType) {
            $roleArName = $adminRole->ar_name ?? $adminRole->name;
            $key = "1:Admin"; // Admin user_type_id = 1
            $adminDescription = $adminType->description ?? 'مدير النظام';
            $combinedOptions[$key] = "{$adminDescription} - {$roleArName}";
        }

        // Staff type (user_type_id = 4) can have Staff role
        $staffRole = $allRoles->get('Staff');
        if ($staffRole && $staffType) {
            $roleArName = $staffRole->ar_name ?? $staffRole->name;
            $key = "4:Staff"; // Staff user_type_id = 4
            $staffDescription = $staffType->description ?? 'موظف إداري';
            $combinedOptions[$key] = "{$staffDescription} - {$roleArName}";
        }

        // Get current user's role for pre-selection
        $currentRole = $user->roles->first();
        $currentUserTypeRole = $currentRole
            ? "{$user->user_type_id}:{$currentRole->name}"
            : null;

        // Get all permissions grouped by module (only if user can manage permissions)
        $permissions = [];
        $userPermissions = [];
        $moduleLabels = [];

        $currentUser = Auth::user();
        if ($currentUser && $currentUser->can('users.manage_permissions')) {
            $permissions = Permission::orderBy('name')
                ->get()
                ->groupBy(function ($permission) {
                    return explode('.', $permission->name)[0];
                });

            // Get user's current direct permissions
            $userPermissions = $user->permissions->pluck('id')->toArray();

            // Arabic labels for modules (for nicer grouping titles in the UI)
            $moduleLabels = [
                'users' => 'المستخدمون',
                'suppliers' => 'الموردون',
                'buyers' => 'المشترون',
                'rfqs' => 'طلبات عروض الأسعار',
                'quotations' => 'عروض الأسعار',
                'orders' => 'الطلبات',
                'invoices' => 'الفواتير',
                'payments' => 'المدفوعات',
                'deliveries' => 'عمليات التسليم',
                'products' => 'المنتجات',
                'manufacturers' => 'الشركات المصنعة',
                'categories' => 'الفئات',
                'activity_logs' => 'سجل النشاط',
                'notifications' => 'الإشعارات',
                'settings' => 'الإعدادات',
                'reports' => 'التقارير',
                'roles' => 'الأدوار',
                'permissions' => 'الصلاحيات',
            ];
        }

        return view('admin.users.edit', compact('user', 'combinedOptions', 'currentUserTypeRole', 'permissions', 'userPermissions', 'moduleLabels'));
    }

    /**
     * 🔄 تحديث بيانات المستخدم
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $data['updated_by'] = Auth::id();
            $user->update($data);

            // Extract user_type_id and role from combined field (if provided)
            $userTypeId = null;
            $roleName = null;

            if ($request->filled('user_type_role')) {
                // Format: "user_type_id:role_name"
                $parts = explode(':', $request->user_type_role);
                if (count($parts) === 2) {
                    $userTypeId = (int) $parts[0];
                    $roleName = $parts[1];
                    $data['user_type_id'] = $userTypeId;
                }
            } elseif ($request->filled('role')) {
                // Fallback: if separate role field exists
                $roleName = $request->role;
            }

            // 🔄 تحديث الدور
            if ($roleName) {
                $user->syncRoles([$roleName]);
            }

            // 🧾 النشاط
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'user_type_id' => $userTypeId ?? $user->user_type_id,
                    'role' => $roleName ?? 'غير محدد',
                    'email' => $user->email,
                ])
                ->log('✏️ تم تحديث بيانات المستخدم');

            // 🔔 إشعار المستخدم
            NotificationService::send(
                $user,
                '✏️ تم تحديث بيانات حسابك',
                'تم تعديل بياناتك في النظام من قبل الإدارة.',
                route('admin.users')
            );

            DB::commit();

            return redirect()
                ->route('admin.users')
                ->with('success', '✅ تم تحديث بيانات المستخدم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث المستخدم: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف المستخدم
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Never delete the system administrator
        $systemAdminEmails = array_map('strtolower', [
            config('app.system_admin_email', 'admin@medequip.com'),
            'admin@medequip.com',
            'admin@MedEquip.com',
        ]);
        if (in_array(strtolower($user->email), $systemAdminEmails)) {
            abort(403, 'لا يمكن حذف حساب مدير النظام.');
        }

        // التأكد من أن المستخدم المراد حذفه هو موظف إداري فقط
        // Admin (user_type_id = 1) and Staff (user_type_id = 4) only
        if (!in_array($user->user_type_id, [1, 4])) {
            abort(403, 'يمكن حذف الموظفين الإداريين فقط من هذه الصفحة. الموردين والمشترين يتم إدارتهم من أقسام منفصلة.');
        }

        try {
            $user->delete();

            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('🗑️ تم حذف المستخدم');

            return redirect()
                ->route('admin.users')
                ->with('success', '❌ تم حذف المستخدم بنجاح.');
        } catch (\Throwable $e) {
            Log::error('User delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف المستخدم: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل المستخدم
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        // التأكد من أن المستخدم المراد عرضه هو موظف إداري فقط
        // Admin (user_type_id = 1) and Staff (user_type_id = 4) only
        if (!in_array($user->user_type_id, [1, 4])) {
            abort(403, 'يمكن عرض الموظفين الإداريين فقط من هذه الصفحة. الموردين والمشترين يتم إدارتهم من أقسام منفصلة.');
        }

        $user->load(['type', 'creator', 'updater', 'roles', 'permissions']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * 📥 تصدير المستخدمين إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        $this->authorize('viewAny', User::class);

        $filters = request()->only(['search', 'role', 'status']);

        return Excel::download(
            new AdminUsersExport($filters),
            'users_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * 🔐 تحديث صلاحيات المستخدم
     */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $this->authorize('managePermissions', $user);

        // Only Staff (user_type_id = 4) can have permissions managed
        if (($user->user_type_id ?? null) !== 4) {
            abort(403, 'يمكن تعيين الصلاحيات للموظفين (Staff) فقط');
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::beginTransaction();

        try {
            $requestedPermissionIds = $validated['permissions'] ?? [];

            // SECURITY: Filter permissions to admin-only (no supplier/buyer permissions)
            $adminPermissionService = app(\App\Services\AdminPermissionService::class);
            $adminPermissionIds = $adminPermissionService->getAdminPermissions()->pluck('id')->toArray();

            // Only allow admin permissions to be assigned
            $validPermissionIds = array_intersect($requestedPermissionIds, $adminPermissionIds);

            // Warn if any permissions were filtered out
            $filteredCount = count($requestedPermissionIds) - count($validPermissionIds);
            if ($filteredCount > 0) {
                Log::warning('Filtered out non-admin permissions', [
                    'user_id' => $user->id,
                    'filtered_count' => $filteredCount,
                    'admin_user' => Auth::id(),
                ]);
            }

            if (!empty($validPermissionIds)) {
                $permissions = Permission::whereIn('id', $validPermissionIds)->get();
                $user->syncPermissions($permissions);
            } else {
                $user->syncPermissions([]);
            }

            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'permissions_count' => count($validPermissionIds),
                    'permission_names' => $permissions->pluck('name')->toArray() ?? [],
                ])
                ->log('🔐 تم تحديث صلاحيات المستخدم');

            DB::commit();

            $successMessage = '✅ تم تحديث صلاحيات المستخدم بنجاح.';
            if ($filteredCount > 0) {
                $successMessage .= " (تم تجاهل {$filteredCount} صلاحية غير مسموحة)";
            }

            return redirect()
                ->route('admin.users.edit', $user)
                ->with('success', $successMessage);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User permissions update error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()]);
        }
    }
}
