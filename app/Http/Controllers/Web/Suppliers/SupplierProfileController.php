<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Supplier Profile Controller
 *
 * Handles supplier profile viewing and editing.
 */
class SupplierProfileController extends Controller
{
    /**
     * Display the supplier's profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        // Load media
        $supplier->load('media');

        // Get verification documents
        $verificationDocuments = $supplier->getMedia('verification_documents');
        $supplierImages = $supplier->getMedia('supplier_images');

        // Log activity
        activity('supplier_profile')
            ->performedOn($supplier)
            ->causedBy($user)
            ->withProperties([
                'supplier_id' => $supplier->id,
                'company_name' => $supplier->company_name,
            ])
            ->log('عرض المورد الملف الشخصي');

        return view('supplier.profile.show', compact('user', 'supplier', 'verificationDocuments', 'supplierImages'));
    }

    /**
     * Show the form for editing the supplier's profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $supplier->load('media');

        // Log activity
        activity('supplier_profile')
            ->performedOn($supplier)
            ->causedBy($user)
            ->withProperties([
                'supplier_id' => $supplier->id,
                'company_name' => $supplier->company_name,
            ])
            ->log('فتح المورد صفحة تعديل الملف الشخصي');

        return view('supplier.profile.edit', compact('user', 'supplier'));
    }

    /**
     * Update the supplier's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $validated = $request->validate([
            // Company Information
            'company_name' => 'required|string|max:255',
            'commercial_register' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',

            // Location
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',

            // Contact
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',

            // User Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,

            // Logo/Image
            'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ], [
            'company_name.required' => 'اسم الشركة مطلوب',
            'contact_email.required' => 'البريد الإلكتروني للتواصل مطلوب',
            'contact_email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'name.required' => 'اسم المستخدم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'logo.image' => 'يجب أن يكون الملف صورة',
            'logo.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
        ]);

        DB::beginTransaction();

        try {
            // Track changes
            $userChanges = [];
            $supplierChanges = [];

            // Update user information
            if ($user->name !== $validated['name']) {
                $userChanges['name'] = ['old' => $user->name, 'new' => $validated['name']];
            }
            if ($user->email !== $validated['email']) {
                $userChanges['email'] = ['old' => $user->email, 'new' => $validated['email']];
            }

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update supplier information
            $oldSupplierData = $supplier->only(['company_name', 'commercial_register', 'tax_number', 'country', 'city', 'address', 'contact_email', 'contact_phone']);

            $supplier->update([
                'company_name' => $validated['company_name'],
                'commercial_register' => $validated['commercial_register'],
                'tax_number' => $validated['tax_number'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
            ]);

            // Track supplier changes
            foreach ($oldSupplierData as $key => $oldValue) {
                if ($oldValue != $supplier->$key) {
                    $supplierChanges[$key] = ['old' => $oldValue, 'new' => $supplier->$key];
                }
            }

            $logoUploaded = false;
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $supplier->clearMediaCollection('supplier_images');
                $supplier->addMediaFromRequest('logo')
                    ->toMediaCollection('supplier_images');
                $logoUploaded = true;
            }

            // Notify admins if significant changes
            if (!empty($userChanges) || !empty($supplierChanges) || $logoUploaded) {
                NotificationService::notifyAdmins(
                    '✏ تحديث ملف مورد',
                    "قام المورد {$supplier->company_name} بتحديث ملفه الشخصي. قد تحتاج إلى مراجعة التغييرات.",
                    route('admin.suppliers.show', $supplier->id)
                );
            }

            // Log activity
            activity('supplier_profile')
                ->performedOn($supplier)
                ->causedBy($user)
                ->withProperties([
                    'supplier_id' => $supplier->id,
                    'company_name' => $supplier->company_name,
                    'user_changes' => $userChanges,
                    'supplier_changes' => $supplierChanges,
                    'logo_uploaded' => $logoUploaded,
                ])
                ->log('قام المورد بتحديث الملف الشخصي');

            DB::commit();

            return redirect()
                ->route('supplier.profile.show')
                ->with('success', 'تم تحديث الملف الشخصي بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier profile update error', [
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الملف الشخصي']);
        }
    }

    /**
     * Update the supplier's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = Auth::user();
        $supplier = $user->supplierProfile;

        DB::beginTransaction();

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Log activity
            activity('supplier_profile')
                ->performedOn($supplier)
                ->causedBy($user)
                ->withProperties([
                    'supplier_id' => $supplier->id,
                    'company_name' => $supplier->company_name,
                ])
                ->log('قام المورد بتغيير كلمة المرور');

            DB::commit();

            return redirect()
                ->route('supplier.profile.show')
                ->with('success', 'تم تغيير كلمة المرور بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Supplier password update error', [
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء تغيير كلمة المرور. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Upload verification document.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'لا يوجد ملف تعريف للمورد');
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'document.required' => 'يرجى اختيار ملف',
            'document.mimes' => 'يجب أن يكون الملف من نوع PDF أو صورة',
            'document.max' => 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت',
        ]);

        try {
            $media = $supplier->addMediaFromRequest('document')
                ->toMediaCollection('verification_documents');

            // Notify admins
            NotificationService::notifyAdmins(
                '📄 مستند تحقق جديد',
                "رفع المورد {$supplier->company_name} مستند تحقق جديد: {$media->file_name}. يحتاج إلى مراجعة.",
                route('admin.suppliers.show', $supplier->id)
            );

            // Log activity
            activity('supplier_profile')
                ->performedOn($supplier)
                ->causedBy(Auth::user())
                ->withProperties([
                    'supplier_id' => $supplier->id,
                    'company_name' => $supplier->company_name,
                    'document_name' => $media->file_name,
                    'document_size' => $media->human_readable_size,
                ])
                ->log('رفع المورد مستند تحقق: ' . $media->file_name);

            return redirect()
                ->route('supplier.profile.show')
                ->with('success', 'تم رفع المستند بنجاح');

        } catch (\Throwable $e) {
            Log::error('Supplier document upload error', [
                'supplier_id' => $supplier->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء رفع المستند. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Delete a verification document.
     */
    public function deleteDocument(Request $request, $mediaId): RedirectResponse
    {
        try {
            $supplier = Auth::user()->supplierProfile;

            if (!$supplier) {
                abort(403, 'لا يوجد ملف تعريف للمورد');
            }

            $media = $supplier->getMedia('verification_documents')->where('id', $mediaId)->first();

            if ($media) {
                $documentName = $media->file_name;
                $media->delete();

                // Log activity
                activity('supplier_profile')
                    ->performedOn($supplier)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'supplier_id' => $supplier->id,
                        'company_name' => $supplier->company_name,
                        'document_name' => $documentName,
                    ])
                    ->log('حذف المورد مستند تحقق: ' . $documentName);

                return redirect()
                    ->route('supplier.profile.show')
                    ->with('success', 'تم حذف المستند بنجاح');
            }

            return redirect()
                ->route('supplier.profile.show')
                ->withErrors(['error' => 'المستند غير موجود']);

        } catch (\Throwable $e) {
            Log::error('Supplier document delete error', [
                'supplier_id' => Auth::user()->supplierProfile?->id,
                'media_id' => $mediaId,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء حذف المستند. يرجى المحاولة مرة أخرى.']);
        }
    }
}

