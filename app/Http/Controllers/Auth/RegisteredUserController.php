<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyerRegistrationRequest;
use App\Http\Requests\SupplierRegistrationRequest;
use App\Models\Buyer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * 🔐 متحكم تسجيل المستخدمين الجدد
 *
 * يدعم تسجيل نوعين من المستخدمين:
 * - المشترين (Buyers): المؤسسات الصحية
 * - الموردين (Suppliers): شركات المعدات الطبية
 */
class RegisteredUserController extends Controller
{
    /**
     * 📄 عرض صفحة التسجيل
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * 💾 معالجة طلب تسجيل مشتري جديد (Buyer)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeBuyer(BuyerRegistrationRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get buyer user type
            $buyerType = UserType::where('slug', 'buyer')->first();
            if (! $buyerType) {
                throw new \Exception('نوع المستخدم "مشتري" غير موجود في النظام');
            }

            // 1️⃣ إنشاء حساب المستخدم
            $user = User::create([
                'user_type_id' => $buyerType->id, // 3
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            // 2️⃣ إنشاء ملف المشتري
            $buyer = Buyer::create([
                'user_id' => $user->id,
                'organization_name' => $request->organization_name,
                'organization_type' => $request->organization_type,
                'license_number' => $request->license_number,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'contact_email' => $request->contact_email ?? $request->email,
                'contact_phone' => $request->contact_phone ?? $request->phone,
                'is_verified' => false, // يحتاج موافقة الإدارة
            ]);

            if ($request->hasFile('license_document')) {
                $buyer->addMediaFromRequest('license_document')
                    ->toMediaCollection('license_documents', 'public');
            }

            // 3️⃣ إسناد دور Buyer للمستخدم
            if (!$user->hasRole('Buyer')) {
                $user->assignRole('Buyer');
            }

            // 4️⃣ إطلاق حدث التسجيل
            event(new Registered($user));

            // 5️⃣ تسجيل الدخول تلقائيًا
            Auth::login($user);

            DB::commit();

            // ✅ Redirect to waiting approval page since buyer needs admin approval
            return redirect()->route('auth.waiting-approval')->with('success', '🎉 تم تسجيل حسابك بنجاح! طلبك قيد المراجعة.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Buyer registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ حدث خطأ أثناء التسجيل: '.$e->getMessage()]);
        }
    }

    /**
     * 💾 معالجة طلب تسجيل مورد جديد (Supplier)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeSupplier(SupplierRegistrationRequest $request): RedirectResponse
    {
        // // 🔍 DEBUG: Log that method was called
        // \Log::info('=== SUPPLIER REGISTRATION STARTED ===');
        // \Log::info('Request data:', $request->except(['password', 'password_confirmation']));

        try {
            DB::beginTransaction();
            \Log::info('Database transaction started');

            // Get supplier user type
            $supplierType = UserType::where('slug', 'supplier')->first();
            if (! $supplierType) {
                throw new \Exception('نوع المستخدم "مورد" غير موجود في النظام');
            }
            $userTypeId = $supplierType->id;
            \Log::info('User type ID for supplier:', ['user_type_id' => $userTypeId]);

            $user = User::create([
                'user_type_id' => $userTypeId, // 2
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);
            \Log::info('User created successfully:', ['user_id' => $user->id, 'email' => $user->email]);

            // 2️⃣ إنشاء ملف المورد
            $supplier = Supplier::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'commercial_register' => $request->commercial_register,
                'tax_number' => $request->tax_number,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'contact_email' => $request->contact_email ?? $request->email,
                'contact_phone' => $request->contact_phone ?? $request->phone,
                'is_verified' => false, // يحتاج موافقة الإدارة
            ]);
            \Log::info('Supplier created successfully:', ['supplier_id' => $supplier->id, 'company_name' => $supplier->company_name]);

            if ($request->hasFile('verification_document')) {
                $supplier->addMediaFromRequest('verification_document')
                    ->toMediaCollection('verification_documents', 'public');
            }

            // 3️⃣ إسناد دور Supplier للمستخدم
            if (!$user->hasRole('Supplier')) {
                $user->assignRole('Supplier');
            }

            // 4️⃣ إطلاق حدث التسجيل
            event(new Registered($user));
            \Log::info('Registered event fired');

            // 5️⃣ تسجيل الدخول تلقائيًا
            Auth::login($user);
            \Log::info('User logged in successfully');

            DB::commit();
            \Log::info('Database transaction committed successfully');
            \Log::info('=== SUPPLIER REGISTRATION COMPLETED SUCCESSFULLY ===');

            // ✅ Redirect to waiting approval page since supplier needs admin approval
            return redirect()->route('auth.waiting-approval')->with('success', '🎉 تم تسجيل حسابك بنجاح! طلبك قيد المراجعة.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Database transaction rolled back');

            // Log the error for debugging
            \Log::error('Supplier registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ حدث خطأ أثناء التسجيل: '.$e->getMessage()]);
        }
    }
}
