<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Supplier') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'اسم الشركة مطلوب',
            'company_name.max' => 'اسم الشركة يجب ألا يتجاوز 255 حرف.',
            'commercial_register.max' => 'السجل التجاري يجب ألا يتجاوز 100 حرف.',
            'tax_number.max' => 'الرقم الضريبي يجب ألا يتجاوز 100 حرف.',
            'country.max' => 'الدولة يجب ألا تتجاوز 100 حرف.',
            'city.max' => 'المدينة يجب ألا تتجاوز 100 حرف.',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرف.',
            'contact_email.required' => 'البريد الإلكتروني للتواصل مطلوب',
            'contact_email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'contact_email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف.',
            'contact_phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف.',
            'name.required' => 'اسم المستخدم مطلوب',
            'name.max' => 'اسم المستخدم يجب ألا يتجاوز 255 حرف.',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف.',
            'logo.image' => 'يجب أن يكون الملف صورة',
            'logo.mimes' => 'الصورة يجب أن تكون بصيغة jpeg أو png أو jpg',
            'logo.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
        ];
    }
}
