<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyerRequest;
use App\Models\Buyer;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class BuyerController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📜 عرض قائمة المشترين
     */
    public function index()
    {
        $buyers = Buyer::with(['user', 'rfqs', 'orders'])
            ->latest('id')
            ->paginate(15);

        return view('admin.buyers.index', compact('buyers'));
    }

    /**
     * ➕ إنشاء مشتري جديد
     */
    public function create()
    {
        return view('admin.buyers.create');
    }

    /**
     * 💾 تخزين مشتري جديد
     */
    public function store(BuyerRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // 1️⃣ إنشاء حساب المستخدم
            $user = User::create([
                'user_type_id' => UserType::where('slug', 'buyer')->first()->id, // 3
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // 2️⃣ إنشاء ملف المشتري
            $buyer = Buyer::create([
                'user_id' => $user->id,
                'organization_name' => $data['organization_name'],
                'organization_type' => $data['organization_type'],
                'license_number' => $data['license_number'] ?? null,
                'country' => $data['country'],
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'is_verified' => $data['is_verified'] ?? true, // Admin-created buyers are verified by default
                'verified_at' => ($data['is_verified'] ?? true) ? now() : null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // 3️⃣ إسناد دور Buyer للمستخدم
            if (! $user->hasRole('Buyer')) {
                $user->assignRole('Buyer');
            }

            // 4️⃣ سجل النشاط
            activity('buyers')
                ->performedOn($buyer)
                ->causedBy(auth()->user())
                ->withProperties([
                    'buyer_name' => $buyer->organization_name,
                    'created_by' => auth()->user()->name,
                ])
                ->log('🟢 تم إنشاء مشتري جديد');

            // 5️⃣ إشعارات
            NotificationService::notifyAdmins(
                '🛍️ مشتري جديد تمت إضافته',
                "تم تسجيل مشتري جديد باسم {$buyer->organization_name}.",
                route('admin.buyers.show', $buyer->id)
            );

            NotificationService::send(
                $user,
                '🎉 تم تسجيلك كمشتري',
                'تم ربط حسابك بنجاح كمشتري في المنصة. يمكنك الآن إنشاء طلبات عروض الأسعار (RFQs).',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.buyers')
                ->with('success', '✅ تم إضافة المشتري بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer store error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * ✏️ تعديل مشتري
     */
    public function edit(Buyer $buyer)
    {
        $buyer->load('user');

        return view('admin.buyers.edit', compact('buyer'));
    }

    /**
     * 🔄 تحديث بيانات المشتري
     */
    public function update(BuyerRequest $request, Buyer $buyer)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // 1️⃣ تحديث بيانات المستخدم
            $buyer->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $buyer->user->phone,
                'updated_by' => auth()->id(),
            ]);

            // تحديث كلمة المرور إذا تم إدخالها
            if (! empty($data['password'])) {
                $buyer->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            // 2️⃣ تحديث بيانات المشتري
            $buyer->update([
                'organization_name' => $data['organization_name'],
                'organization_type' => $data['organization_type'],
                'license_number' => $data['license_number'] ?? $buyer->license_number,
                'country' => $data['country'],
                'city' => $data['city'] ?? $buyer->city,
                'address' => $data['address'] ?? $buyer->address,
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'is_verified' => $data['is_verified'] ?? $buyer->is_verified,
                'verified_at' => ($data['is_verified'] ?? false) && ! $buyer->is_verified ? now() : $buyer->verified_at,
                'is_active' => $data['is_active'] ?? $buyer->is_active,
                'updated_by' => auth()->id(),
            ]);

            // 3️⃣ سجل النشاط
            activity('buyers')
                ->performedOn($buyer)
                ->causedBy(auth()->user())
                ->withProperties([
                    'buyer_name' => $buyer->organization_name,
                    'updated_by' => auth()->user()->name,
                ])
                ->log('🟡 تم تحديث بيانات المشتري');

            // 4️⃣ إشعار المستخدم المرتبط
            NotificationService::send(
                $buyer->user,
                '✏️ تم تحديث بيانات حسابك كمشتري',
                'تم تعديل بيانات حسابك من قبل الإدارة.',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.buyers')
                ->with('success', '✅ تم تحديث بيانات المشتري بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer update error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل تحديث بيانات المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * 🗑️ حذف المشتري
     */
    public function destroy(Buyer $buyer)
    {
        try {
            $buyer->delete();

            activity('buyers')
                ->performedOn($buyer)
                ->causedBy(auth()->user())
                ->log('❌ تم حذف المشتري');

            return redirect()
                ->route('admin.buyers')
                ->with('success', '❌ تم حذف المشتري بنجاح');
        } catch (\Throwable $e) {
            Log::error('Buyer delete error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل حذف المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * 👁️ عرض تفاصيل المشتري
     */
    public function show(Buyer $buyer)
    {
        $buyer->load(['user', 'rfqs', 'orders']);

        return view('admin.buyers.show', compact('buyer'));
    }
}
