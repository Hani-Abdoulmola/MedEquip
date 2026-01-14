<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyers\BuyerProfileUpdateRequest;
use App\Models\Buyer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Buyer Profile Controller
 * 
 * Handles buyer profile management including viewing, editing,
 * password updates, and document uploads.
 * 
 * Note: Buyer verification is handled by the 'buyer.verified' middleware.
 */
class BuyerProfileController extends Controller
{
    /**
     * Display the buyer's profile.
     */
    public function show(): View
    {
        $this->authorize('viewProfile', Buyer::class);

        $buyer = Auth::user()->buyerProfile;
        $buyer->load(['user', 'media']);

        // Get license documents
        $licenseDocuments = $buyer->getMedia('license_documents');

        return view('buyer.profile.show', compact('buyer', 'licenseDocuments'));
    }

    /**
     * Show the form for editing the buyer's profile.
     */
    public function edit(): View
    {
        $this->authorize('editProfile', Buyer::class);

        $buyer = Auth::user()->buyerProfile;
        $buyer->load(['user', 'media']);

        // Organization types
        $organizationTypes = [
            'hospital' => 'مستشفى',
            'clinic' => 'عيادة',
            'pharmacy' => 'صيدلية',
            'laboratory' => 'مختبر',
            'medical_center' => 'مركز طبي',
            'distributor' => 'موزع',
            'other' => 'أخرى',
        ];

        // Get license documents
        $licenseDocuments = $buyer->getMedia('license_documents');

        return view('buyer.profile.edit', compact('buyer', 'organizationTypes', 'licenseDocuments'));
    }

    /**
     * Update the buyer's profile.
     */
    public function update(BuyerProfileUpdateRequest $request): RedirectResponse
    {
        $this->authorize('editProfile', Buyer::class);

        $buyer = Auth::user()->buyerProfile;

        DB::beginTransaction();

        try {
            // Update buyer profile (excluding protected fields)
            $data = $request->validated();
            unset($data['license_documents']); // Handle separately

            $data['updated_by'] = Auth::id();

            $buyer->update($data);

            // Handle license document uploads
            if ($request->hasFile('license_documents')) {
                foreach ($request->file('license_documents') as $file) {
                    $buyer->addMedia($file)
                        ->toMediaCollection('license_documents');
                }
            }

            // Log activity
            activity('buyer_profile')
                ->performedOn($buyer)
                ->causedBy(Auth::user())
                ->withProperties([
                    'buyer_id' => $buyer->id,
                    'updated_fields' => array_keys($data),
                ])
                ->log('قام المشتري بتحديث ملفه الشخصي');

            DB::commit();

            return redirect()
                ->route('buyer.profile.show')
                ->with('success', '✅ تم تحديث الملف الشخصي بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer profile update error', [
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the buyer's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $this->authorize('updatePassword', Buyer::class);

        $buyer = Auth::user()->buyerProfile;

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log activity
        activity('buyer_profile')
            ->performedOn($buyer)
            ->causedBy($user)
            ->log('قام المشتري بتحديث كلمة المرور');

        return back()->with('success', '✅ تم تحديث كلمة المرور بنجاح.');
    }

    /**
     * Upload a license document.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $this->authorize('manageDocuments', Buyer::class);

        $buyer = Auth::user()->buyerProfile;

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'document.required' => 'يرجى اختيار ملف.',
            'document.file' => 'يجب أن يكون الملف ملفاً صالحاً.',
            'document.mimes' => 'يجب أن يكون الملف بصيغة PDF أو JPG أو PNG.',
            'document.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
        ]);

        try {
            $buyer->addMedia($request->file('document'))
                ->toMediaCollection('license_documents');

            // Log activity
            activity('buyer_profile')
                ->performedOn($buyer)
                ->causedBy(Auth::user())
                ->log('قام المشتري برفع وثيقة ترخيص');

            return back()->with('success', '✅ تم رفع الوثيقة بنجاح.');

        } catch (\Throwable $e) {
            Log::error('Buyer document upload error', [
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['document' => 'حدث خطأ أثناء رفع الملف.']);
        }
    }

    /**
     * Delete a license document.
     */
    public function deleteDocument(int $mediaId): RedirectResponse
    {
        $this->authorize('manageDocuments', Buyer::class);

        $buyer = Auth::user()->buyerProfile;

        try {
            $media = $buyer->media()->findOrFail($mediaId);
            $media->delete();

            // Log activity
            activity('buyer_profile')
                ->performedOn($buyer)
                ->causedBy(Auth::user())
                ->log('قام المشتري بحذف وثيقة ترخيص');

            return back()->with('success', '✅ تم حذف الوثيقة بنجاح.');

        } catch (\Throwable $e) {
            Log::error('Buyer document delete error', [
                'buyer_id' => $buyer->id,
                'media_id' => $mediaId,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف الملف.']);
        }
    }
}

