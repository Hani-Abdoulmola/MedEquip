<?php

namespace App\Http\Requests\Buyers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Buyer Profile Update Request
 * 
 * Validates buyer profile updates.
 * Note: Buyers cannot change is_verified, is_active, or rejection_reason fields.
 */
class BuyerProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $buyer = Auth::user()?->buyerProfile;
        return $buyer !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $buyer = Auth::user()->buyerProfile;

        return [
            // Organization Details
            'organization_name' => ['required', 'string', 'max:200'],
            'organization_type' => [
                'required',
                Rule::in(['hospital', 'clinic', 'pharmacy', 'laboratory', 'medical_center', 'distributor', 'other']),
            ],
            'license_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('buyers', 'license_number')->ignore($buyer->id),
            ],

            // Location
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],

            // Contact Information
            'contact_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('buyers', 'contact_email')->ignore($buyer->id),
            ],
            'contact_phone' => ['required', 'string', 'max:20'],

            // License Documents (optional update)
            'license_documents' => ['nullable', 'array', 'max:5'],
            'license_documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB max per file
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Organization Name
            'organization_name.required' => 'اسم المنظمة مطلوب.',
            'organization_name.string' => 'اسم المنظمة يجب أن يكون نصاً.',
            'organization_name.max' => 'اسم المنظمة يجب ألا يتجاوز 200 حرف.',

            // Organization Type
            'organization_type.required' => 'نوع المنظمة مطلوب.',
            'organization_type.in' => 'نوع المنظمة غير صالح.',

            // License Number
            'license_number.required' => 'رقم الترخيص مطلوب.',
            'license_number.string' => 'رقم الترخيص يجب أن يكون نصاً.',
            'license_number.max' => 'رقم الترخيص يجب ألا يتجاوز 100 حرف.',
            'license_number.unique' => 'رقم الترخيص مستخدم بالفعل.',

            // Country
            'country.required' => 'الدولة مطلوبة.',
            'country.string' => 'الدولة يجب أن تكون نصاً.',
            'country.max' => 'الدولة يجب ألا تتجاوز 100 حرف.',

            // City
            'city.required' => 'المدينة مطلوبة.',
            'city.string' => 'المدينة يجب أن تكون نصاً.',
            'city.max' => 'المدينة يجب ألا تتجاوز 100 حرف.',

            // Address
            'address.required' => 'العنوان مطلوب.',
            'address.string' => 'العنوان يجب أن يكون نصاً.',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرف.',

            // Contact Email
            'contact_email.required' => 'البريد الإلكتروني للتواصل مطلوب.',
            'contact_email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'contact_email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف.',
            'contact_email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',

            // Contact Phone
            'contact_phone.required' => 'رقم الهاتف للتواصل مطلوب.',
            'contact_phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',
            'contact_phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف.',

            // License Documents
            'license_documents.array' => 'وثائق الترخيص يجب أن تكون مصفوفة.',
            'license_documents.max' => 'يمكنك رفع 5 وثائق كحد أقصى.',
            'license_documents.*.file' => 'يجب أن يكون الملف ملفاً صالحاً.',
            'license_documents.*.mimes' => 'يجب أن يكون الملف بصيغة PDF أو JPG أو PNG.',
            'license_documents.*.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'organization_name' => 'اسم المنظمة',
            'organization_type' => 'نوع المنظمة',
            'license_number' => 'رقم الترخيص',
            'country' => 'الدولة',
            'city' => 'المدينة',
            'address' => 'العنوان',
            'contact_email' => 'البريد الإلكتروني',
            'contact_phone' => 'رقم الهاتف',
            'license_documents' => 'وثائق الترخيص',
        ];
    }
}

