<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class SupplierController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📜 عرض قائمة الموردين
     */
    public function index()
    {
        $suppliers = Supplier::with(['user', 'products'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * ➕ صفحة إنشاء مورد جديد
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * 💾 تخزين المورد الجديد
     */
    public function store(SupplierRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // 1️⃣ إنشاء حساب المستخدم
            $user = User::create([
                'user_type_id' => UserType::where('slug', 'supplier')->first()->id, // 2
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // 2️⃣ إنشاء ملف المورد
            $supplier = Supplier::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'commercial_register' => $data['commercial_register'],
                'tax_number' => $data['tax_number'] ?? null,
                'country' => $data['country'],
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'is_verified' => $data['is_verified'] ?? true, // Admin-created suppliers are verified by default
                'verified_at' => ($data['is_verified'] ?? true) ? now() : null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // 3️⃣ إسناد دور Supplier للمستخدم
            if (! $user->hasRole('Supplier')) {
                $user->assignRole('Supplier');
            }

            // 4️⃣ سجل النشاط
            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy(auth()->user())
                ->withProperties([
                    'company_name' => $supplier->company_name,
                    'created_by' => auth()->user()->name,
                ])
                ->log('🟢 تم إنشاء مورد جديد');

            // 5️⃣ إشعارات
            NotificationService::notifyAdmins(
                '🏭 مورد جديد تمت إضافته',
                "تم تسجيل مورد جديد باسم {$supplier->company_name}.",
                route('admin.suppliers.show', $supplier->id)
            );

            NotificationService::send(
                $user,
                '🎉 تم تسجيلك كمورد',
                'تم ربط حسابك بنجاح كمورد في المنصة. يمكنك الآن إضافة منتجاتك وتقديم عروض الأسعار.',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.suppliers')
                ->with('success', '✅ تم إضافة المورد بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier store error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * ✏️ صفحة تعديل المورد
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load('user');

        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * 🔄 تحديث بيانات المورد
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // 1️⃣ تحديث بيانات المستخدم
            $supplier->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $supplier->user->phone,
                'updated_by' => auth()->id(),
            ]);

            // تحديث كلمة المرور إذا تم إدخالها
            if (! empty($data['password'])) {
                $supplier->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            // 2️⃣ تحديث بيانات المورد
            $supplier->update([
                'company_name' => $data['company_name'],
                'commercial_register' => $data['commercial_register'],
                'tax_number' => $data['tax_number'] ?? $supplier->tax_number,
                'country' => $data['country'],
                'city' => $data['city'] ?? $supplier->city,
                'address' => $data['address'] ?? $supplier->address,
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'is_verified' => $data['is_verified'] ?? $supplier->is_verified,
                'verified_at' => ($data['is_verified'] ?? false) && ! $supplier->is_verified ? now() : $supplier->verified_at,
                'is_active' => $data['is_active'] ?? $supplier->is_active,
                'updated_by' => auth()->id(),
            ]);

            // 3️⃣ سجل النشاط
            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy(auth()->user())
                ->withProperties([
                    'company_name' => $supplier->company_name,
                    'updated_by' => auth()->user()->name,
                ])
                ->log('🟡 تم تحديث بيانات المورد');

            // 4️⃣ إشعار المستخدم المرتبط
            NotificationService::send(
                $supplier->user,
                '✏️ تم تحديث بيانات حسابك كمورد',
                'تم تعديل بيانات حسابك من قبل الإدارة.',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.suppliers')
                ->with('success', '✅ تم تحديث بيانات المورد بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier update error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل تحديث بيانات المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * 🗑️ حذف المورد
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $companyName = $supplier->company_name;

            $supplier->delete();

            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy(auth()->user())
                ->withProperties(['company_name' => $companyName])
                ->log('❌ تم حذف المورد');

            return redirect()
                ->route('admin.suppliers')
                ->with('success', '❌ تم حذف المورد بنجاح');
        } catch (\Throwable $e) {
            Log::error('Supplier delete error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل حذف المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * 👁️ عرض تفاصيل المورد
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['user', 'products']);

        return view('admin.suppliers.show', compact('supplier'));
    }
}
