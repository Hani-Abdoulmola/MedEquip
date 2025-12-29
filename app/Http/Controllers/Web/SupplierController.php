<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use App\Exports\AdminSuppliersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     *  عرض قائمة الموردين مع فلاتر إدارية بسيطة
     */
    public function index(Request $request): View
    {
        $query = Supplier::with(['user', 'products']);

        // البحث بالاسم أو المدينة
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // فلتر الحالة
        if ($status = $request->get('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'pending') {
                $query->where('is_verified', false);
            } elseif ($status === 'suspended') {
                $query->where('is_active', false);
            }
        }

        // فلتر المدينة
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        $suppliers = $query->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('is_active', true)->count(),
            'pending' => Supplier::where('is_verified', false)->count(),
            'suspended' => Supplier::where('is_active', false)->count(),
        ];

        $cities = Supplier::select('city')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('admin.suppliers.index', compact('suppliers', 'stats', 'cities'));
    }

    /**
     * ➕ صفحة إنشاء مورد جديد
     */
    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    /**
     * 💾 تخزين المورد الجديد
     */
    public function store(SupplierRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Get supplier user type
            $supplierType = UserType::where('slug', 'supplier')->first();
            if (!$supplierType) {
                throw new \Exception('نوع المستخدم "مورد" غير موجود في النظام');
            }

            /** @var \App\Models\User */
            $authUser = Auth::user();

            // 1️ إنشاء حساب المستخدم
            $user = User::create([
                'user_type_id' => $supplierType->id, // 2
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'created_by' => $authUser->id,
            ]);

            //  إنشاء ملف المورد
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
                'created_by' => $authUser->id,
            ]);

            // .ب  حفظ مستند التوثيق (إن وجد)
            if ($request->hasFile('verification_document')) {
                $supplier->addMediaFromRequest('verification_document')
                    ->toMediaCollection('verification_documents');
            }

            //  إسناد دور Supplier للمستخدم
            if (! $user->hasRole('Supplier')) {
                $user->assignRole('Supplier');
            }

            //  سجل النشاط
            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy($authUser)
                ->withProperties([
                    'company_name' => $supplier->company_name,
                    'created_by' => $authUser->name,
                ])
                ->log(' تم إنشاء مورد جديد');

            //  إشعارات
            NotificationService::notifyAdmins(
                ' مورد جديد تمت إضافته',
                "تم تسجيل مورد جديد باسم {$supplier->company_name}.",
                route('admin.suppliers.show', $supplier->id)
            );

            NotificationService::send(
                $user,
                ' تم تسجيلك كمورد',
                'تم ربط حسابك بنجاح كمورد في المنصة. يمكنك الآن إضافة منتجاتك وتقديم عروض الأسعار.',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.suppliers')
                ->with('success', ' تم إضافة المورد بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier store error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  صفحة تعديل المورد
     */
    public function edit(Supplier $supplier): View
    {
        $supplier->load('user');

        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     *  تحديث بيانات المورد
     */
    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            /** @var \App\Models\User */
            $authUser = Auth::user();

            //  تحديث بيانات المستخدم
            $supplier->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $supplier->user->phone,
                'updated_by' => $authUser->id,
            ]);

            // تحديث كلمة المرور إذا تم إدخالها
            if (! empty($data['password'])) {
                $supplier->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            //  تحديث بيانات المورد
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
                'updated_by' => $authUser->id,
            ]);

            //  سجل النشاط
            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy($authUser)
                ->withProperties([
                    'company_name' => $supplier->company_name,
                    'updated_by' => $authUser->name,
                ])
                ->log(' تم تحديث بيانات المورد');

            //  إشعار المستخدم المرتبط
            NotificationService::send(
                $supplier->user,
                ' تم تحديث بيانات حسابك كمورد',
                'تم تعديل بيانات حسابك من قبل الإدارة.',
                route('dashboard')
            );

            DB::commit();

            return redirect()
                ->route('admin.suppliers')
                ->with('success', ' تم تحديث بيانات المورد بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier update error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل تحديث بيانات المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  حذف المورد
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $companyName = $supplier->company_name;

            $supplier->delete();

            /** @var \App\Models\User */
            $authUser = Auth::user();

            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy($authUser)
                ->withProperties(['company_name' => $companyName])
                ->log(' تم حذف المورد');

            return redirect()
                ->route('admin.suppliers')
                ->with('success', ' تم حذف المورد بنجاح');
        } catch (\Throwable $e) {
            Log::error('Supplier delete error: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'فشل حذف المورد: '.$e->getMessage(),
            ]);
        }
    }

    /**
     *  عرض تفاصيل المورد
     */
    public function show(Supplier $supplier): View
    {
        $supplier->load(['user', 'products', 'quotations']);

        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     *  توثيق المورد من قبل الإدارة
     */
    public function verify(Supplier $supplier): RedirectResponse
    {
        if (! $supplier->is_verified) {
            /** @var \App\Models\User */
            $authUser = Auth::user();

            $supplier->update([
                'is_verified' => true,
                'verified_at' => now(),
                'rejection_reason' => null,
                'updated_by' => $authUser->id,
            ]);

            activity('suppliers')
                ->performedOn($supplier)
                ->causedBy($authUser)
                ->withProperties(['company_name' => $supplier->company_name])
                ->log(' تم توثيق المورد من قبل الإدارة');

            NotificationService::send(
                $supplier->user,
                ' تم توثيق حسابك كمورد',
                'تمت مراجعة وتوثيق حسابك من قبل إدارة المنصة. يمكنك الآن استخدام جميع المميزات المتاحة للموردين.',
                route('dashboard')
            );
        }

        return back()->with('success', ' تم توثيق المورد بنجاح');
    }

    /**
     *  تفعيل / إيقاف حساب المورد
     */
    public function toggleActive(Supplier $supplier): RedirectResponse
    {
        $newStatus = ! $supplier->is_active;

        /** @var \App\Models\User */
        $authUser = Auth::user();

        $supplier->update([
            'is_active' => $newStatus,
            'updated_by' => $authUser->id,
        ]);

        activity('suppliers')
            ->performedOn($supplier)
            ->causedBy($authUser)
            ->withProperties([
                'company_name' => $supplier->company_name,
                'is_active' => $newStatus,
            ])
            ->log($newStatus ? ' تم تفعيل حساب المورد' : ' تم إيقاف حساب المورد');

        return back()->with('success', $newStatus ? ' تم تفعيل المورد' : ' تم إيقاف المورد');
    }

    /**
     * 📥 تصدير الموردين إلى Excel
     */
    public function export(): BinaryFileResponse
    {
        $filters = request()->only(['q', 'status', 'city', 'country']);
        
        return Excel::download(
            new AdminSuppliersExport($filters),
            'suppliers_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
