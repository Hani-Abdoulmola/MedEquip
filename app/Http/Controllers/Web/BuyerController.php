<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyerRequest;
use App\Models\Buyer;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use App\Exports\AdminBuyersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BuyerController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     *  عرض قائمة المشترين
     */
    public function index(): View
    {
        // Check permission
        if (!auth()->user()->can('buyers.view')) {
            abort(403, 'ليس لديك صلاحية عرض المشترين');
        }
        
        $query = Buyer::with(['user', 'rfqs', 'orders']);

        // 🔍 Filter by search (organization name, contact email, contact phone, user name, user email)
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('organization_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // 🔍 Filter by active status
        if (request()->filled('active')) {
            $query->where('is_active', request('active') == '1' ? true : false);
        }

        // 🔍 Filter by verification status
        if (request()->filled('verified')) {
            $query->where('is_verified', request('verified') == '1' ? true : false);
        }

        // 🔍 Filter by organization type
        if (request()->filled('type')) {
            $query->where('organization_type', request('type'));
        }

        $buyers = $query->latest('id')->paginate(15)->withQueryString();

        // 📊 Calculate stats
        $stats = [
            'total_buyers' => Buyer::count(),
            'active_buyers' => Buyer::where('is_active', true)->count(),
            'verified_buyers' => Buyer::where('is_verified', true)->count(),
            'pending_buyers' => Buyer::where('is_verified', false)->count(),
        ];

        return view('admin.buyers.index', compact('buyers', 'stats'));
    }

    /**
     *  إنشاء مشتري جديد
     */
    public function create(): View
    {
        return view('admin.buyers.create');
    }

    /**
     *  تخزين مشتري جديد
     */
    public function store(BuyerRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Get buyer user type
            $buyerType = UserType::where('slug', 'buyer')->first();
            if (!$buyerType) {
                throw new \Exception('نوع المستخدم "مشتري" غير موجود في النظام');
            }

            /** @var \App\Models\User */
            $authUser = Auth::user();

            //  إنشاء حساب المستخدم
            $user = User::create([
                'user_type_id' => $buyerType->id, // 3
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'created_by' => $authUser->id,
            ]);

            //  إنشاء ملف المشتري
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
                'created_by' => $authUser->id,
            ]);

            //  إسناد دور Buyer للمستخدم
            if (! $user->hasRole('Buyer')) {
                $user->assignRole('Buyer');
            }

            //  سجل النشاط
            activity('buyers')
                ->performedOn($buyer)
                ->causedBy($authUser)
                ->withProperties([
                    'buyer_name' => $buyer->organization_name,
                    'created_by' => $authUser->name,
                ])
                ->log(' تم إنشاء مشتري جديد');

            // 5️⃣ إشعارات
            NotificationService::notifyAdmins(
                ' مشتري جديد تمت إضافته',
                "تم تسجيل مشتري جديد باسم {$buyer->organization_name}.",
                route('admin.buyers.show', $buyer->id)
            );

            NotificationService::send(
                $user,
                ' تم تسجيلك كمشتري',
                'تم ربط حسابك بنجاح كمشتري في المنصة. يمكنك الآن إنشاء طلبات عروض الأسعار (RFQs).',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.buyers')
                ->with('success', ' تم إضافة المشتري بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer store error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  تعديل مشتري
     */
    public function edit(Buyer $buyer): View
    {
        $buyer->load('user');

        return view('admin.buyers.edit', compact('buyer'));
    }

    /**
     *  تحديث بيانات المشتري
     */
    public function update(BuyerRequest $request, Buyer $buyer): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            /** @var \App\Models\User */
            $authUser = Auth::user();

            //  تحديث بيانات المستخدم
            $buyer->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $buyer->user->phone,
                'updated_by' => $authUser->id,
            ]);

            // تحديث كلمة المرور إذا تم إدخالها
            if (! empty($data['password'])) {
                $buyer->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            //  تحديث بيانات المشتري
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
                'updated_by' => $authUser->id,
            ]);

            //  سجل النشاط`
            activity('buyers')
                ->performedOn($buyer)
                ->causedBy($authUser)
                ->withProperties([
                    'buyer_name' => $buyer->organization_name,
                    'updated_by' => $authUser->name,
                ])
                ->log(' تم تحديث بيانات المشتري');

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
                ->with('success', ' تم تحديث بيانات المشتري بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer update error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل تحديث بيانات المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  حذف المشتري
     */
    public function destroy(Buyer $buyer): RedirectResponse
    {
        try {
            $buyer->delete();

            /** @var \App\Models\User */
            $authUser = Auth::user();

            activity('buyers')
                ->performedOn($buyer)
                ->causedBy($authUser)
                ->log(' تم حذف المشتري');

            return redirect()
                ->route('admin.buyers')
                ->with('success', ' تم حذف المشتري بنجاح');
        } catch (\Throwable $e) {
            Log::error('Buyer delete error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل حذف المشتري: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  عرض تفاصيل المشتري
     */
    public function show(Buyer $buyer): View
    {
        $buyer->load(['user', 'rfqs', 'orders']);

        return view('admin.buyers.show', compact('buyer'));
    }

    /**
     *  تفعيل/تعطيل المشتري
     */
    public function toggleActive(Buyer $buyer): RedirectResponse
    {
        $buyer->is_active = ! $buyer->is_active;
        $buyer->save();

        return back()->with('success', 'تم تحديث حالة المشتري بنجاح');
    }

    /**
     *  توثيق المشتري
     */
    public function verifyBuyer(Buyer $buyer): RedirectResponse
    {
        $buyer->is_verified = true;
        $buyer->verified_at = now();
        $buyer->save();

        return back()->with('success', 'تم توثيق المشتري بنجاح');
    }

    /**
     * 📥 تصدير المشترين إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        $filters = request()->only(['search', 'country', 'status', 'verified']);
        
        return Excel::download(
            new AdminBuyersExport($filters),
            'buyers_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
