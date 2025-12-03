<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحكم في الصلاحيات يتم عبر الـ Middleware
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->has('email') ? strtolower(trim($this->email)) : null,
            'contact_email' => $this->has('contact_email') ? strtolower(trim($this->contact_email)) : null,
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('buyer')?->id;
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
        $userId = $this->route('buyer')?->user_id;

        return [
            // 👤 بيانات حساب المستخدم (للإنشاء من قبل الأدمن)
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8'],

            // 🏢 بيانات المؤسسة
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'string', 'max:100'],
            'license_number' => ['nullable', 'string', 'max:100'],

            // 🌍 الموقع والاتصال
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],

            // ✉️ معلومات التواصل
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => [
                'required',
                'string',
                'max:50',
                'regex:/^[0-9+\-\s()]+$/',
            ],

            // ✅ التحقق والتفعيل
            'is_verified' => ['boolean'],
            'is_active' => ['boolean'],
            'verified_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            // رسائل حساب المستخدم
            'name.required' => 'اسم المستخدم مطلوب.',
            'name.max' => 'اسم المستخدم لا يمكن أن يتجاوز 255 حرفًا.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',

            // رسائل بيانات المؤسسة
            'organization_name.required' => 'اسم المؤسسة مطلوب.',
            'organization_name.max' => 'اسم المؤسسة لا يمكن أن يتجاوز 255 حرفًا.',

            'organization_type.required' => 'نوع المؤسسة مطلوب.',

            'country.required' => 'الدولة مطلوبة.',

            'contact_email.required' => 'البريد الإلكتروني للتواصل مطلوب.',
            'contact_email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',

            'contact_phone.required' => 'رقم الهاتف للتواصل مطلوب.',
            'contact_phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',

            'verified_at.date' => 'تاريخ التحقق غير صحيح.',
        ];
    }

    /**
     * ✅ تحقق إضافي بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->boolean('is_verified') === false && $this->filled('verified_at')) {
                $validator->errors()->add('verified_at', 'لا يمكن تحديد تاريخ التحقق إذا لم يكن الحساب موثقًا.');
            }
        });
    }
}
