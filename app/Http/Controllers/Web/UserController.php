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
     * 🧾 عرض كل المستخدمين
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['type', 'creator', 'updater', 'roles']);

        // 🔍 Filter by user type
        if (request()->filled('user_type')) {
            $query->where('user_type_id', request('user_type'));
        }

        // 🔍 Search by name or email
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🔍 Filter by status
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $users = $query->latest('id')->paginate(15);

        // 📊 Calculate stats
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'suppliers_count' => User::where('user_type_id', 2)->count(), // Supplier type
            'buyers_count' => User::where('user_type_id', 3)->count(), // Buyer type
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * ➕ صفحة إنشاء مستخدم جديد
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $types = UserType::pluck('name', 'id');
        // Only show internal/system roles for staff (exclude Supplier/Buyer identity roles)
        $roles = Role::whereNotIn('name', ['Supplier', 'Buyer'])->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->ar_name ?? $role->name];
        });

        return view('admin.users.create', [
            'types' => $types,
            'roles' => $roles,
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

            $user = User::create($data);

            // 🧩 تعيين الدور (الدور للتصنيف فقط، لا يعطي صلاحيات تلقائياً)
            if ($request->filled('role')) {
                $user->assignRole($request->role);
                // Important: Revoke any role permissions to ensure clean state
                // User will get permissions manually via "الأدوار و الصلاحيات" page
                $user->syncPermissions([]);
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'email' => $user->email,
                    'role' => $request->role ?? 'غير محدد',
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

        $types = UserType::pluck('name', 'id');
        // Only show internal/system roles for staff (exclude Supplier/Buyer identity roles)
        $roles = Role::whereNotIn('name', ['Supplier', 'Buyer'])->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->ar_name ?? $role->name];
        });
        
        // Get all permissions grouped by module (only if user can manage permissions)
        $permissions = [];
        $userPermissions = [];
        $moduleLabels = [];
        
        if (auth()->user()->can('users.manage_permissions')) {
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

        return view('admin.users.edit', compact('user', 'types', 'roles', 'permissions', 'userPermissions', 'moduleLabels'));
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

            // 🔄 تحديث الدور
            if ($request->filled('role')) {
                $user->syncRoles([$request->role]);
            }

            // 🧾 النشاط
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'role' => $request->role ?? 'غير محدد',
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

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::beginTransaction();

        try {
            if (isset($validated['permissions'])) {
                $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->get();
                $user->syncPermissions($permissions);
            } else {
                $user->syncPermissions([]);
            }

            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'permissions_count' => count($validated['permissions'] ?? []),
                ])
                ->log('🔐 تم تحديث صلاحيات المستخدم');

            DB::commit();

            return redirect()
                ->route('admin.users.edit', $user)
                ->with('success', '✅ تم تحديث صلاحيات المستخدم بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User permissions update error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()]);
        }
    }
}
