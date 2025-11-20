<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // فقط المستخدمين المسجلين
    }

    /**
     * 👁️ عرض صفحة تعديل البروفايل
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['roles', 'buyerProfile', 'supplierProfile']);

        return view('profile.edit', compact('user'));
    }

    /**
     * 💾 تحديث بيانات البروفايل
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        try {
            $data = $request->validated();

            // ✉️ إذا تم تعديل البريد الإلكتروني، نلغي التحقق السابق
            if ($user->email !== $data['email']) {
                $user->email_verified_at = null;
            }

            // 🔐 إذا تم إدخال كلمة مرور جديدة
            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->fill($data);
            $user->save();

            // 🔗 تحديث بيانات المورد أو المشتري تلقائيًا لو موجود
            if ($user->hasRole('Supplier') && $user->supplierProfile) {
                $user->supplierProfile->update([
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                ]);
            }

            if ($user->hasRole('Buyer') && $user->buyerProfile) {
                $user->buyerProfile->update([
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                ]);
            }

            // 🧾 سجل النشاط
            activity('profile')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['updated_by' => $user->id])
                ->log('🟢 تم تحديث الملف الشخصي بنجاح');

            return Redirect::route('profile.edit')->with('success', '✅ تم تحديث بياناتك الشخصية بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Profile update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ البيانات: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف حساب المستخدم نهائيًا
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // 🧾 سجل النشاط قبل الحذف
            activity('profile')
                ->performedOn($user)
                ->causedBy($user)
                ->log('❌ المستخدم حذف حسابه');

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('status', 'تم حذف حسابك بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Profile delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف الحساب: '.$e->getMessage()]);
        }
    }
}
