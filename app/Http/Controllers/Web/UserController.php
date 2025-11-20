<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 🧾 عرض كل المستخدمين
     */
    public function index()
    {
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
    public function create()
    {
        $types = UserType::pluck('name', 'id');
        $roles = Role::pluck('name', 'name');

        return view('admin.users.create', [
            'types' => $types,
            'roles' => $roles,
            'user' => new User,
        ]);
    }

    /**
     * 💾 حفظ مستخدم جديد
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['created_by'] = auth()->id();

            $user = User::create($data);

            // 🧩 تعيين الدور
            if ($request->filled('role')) {
                $user->assignRole($request->role);
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
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
    public function edit(User $user)
    {
        $types = UserType::pluck('name', 'id');
        $roles = Role::pluck('name', 'name');

        return view('admin.users.edit', compact('user', 'types', 'roles'));
    }

    /**
     * 🔄 تحديث بيانات المستخدم
     */
    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $data['updated_by'] = auth()->id();
            $user->update($data);

            // 🔄 تحديث الدور
            if ($request->filled('role')) {
                $user->syncRoles([$request->role]);
            }

            // 🧾 النشاط
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
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
    public function destroy(User $user)
    {
        try {
            $user->delete();

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
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
    public function show(User $user)
    {
        $user->load(['type', 'creator', 'updater', 'roles']);

        return view('admin.users.show', compact('user'));
    }
}
