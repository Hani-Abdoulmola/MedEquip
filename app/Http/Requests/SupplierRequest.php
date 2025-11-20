<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحكم في الصلاحيات يتم عبر Middleware و Spatie
    }

    public function rules(): array
    {
        $id = $this->route('supplier')?->id;
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
        $userId = $this->route('supplier')?->user_id;

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

            // 🏢 بيانات الشركة
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'company_name')->ignore($id),
            ],
            'commercial_register' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:100'],

            // 🌍 الموقع ووسائل التواصل
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],

            // 📧 بيانات الاتصال
            'contact_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('suppliers', 'contact_email')->ignore($id),
            ],
            'contact_phone' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'contact_phone')->ignore($id),
                'regex:/^[0-9+\-\s()]+$/',
            ],

            // ✅ حالة التوثيق والتفعيل
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

            // رسائل بيانات الشركة
            'company_name.required' => 'اسم الشركة مطلوب.',
            'company_name.unique' => 'يوجد مورد بنفس اسم الشركة.',

            'commercial_register.required' => 'رقم السجل التجاري مطلوب.',

            'contact_email.required' => 'البريد الإلكتروني للتواصل مطلوب.',
            'contact_email.email' => 'يجب إدخال بريد إلكتروني صالح.',
            'contact_email.unique' => 'هذا البريد مستخدم مسبقًا.',

            'contact_phone.required' => 'رقم الهاتف للتواصل مطلوب.',
            'contact_phone.unique' => 'رقم الهاتف مستخدم مسبقًا.',
            'contact_phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',

            'country.required' => 'اسم الدولة مطلوب.',
            'is_verified.boolean' => 'قيمة التوثيق يجب أن تكون صحيحة (true/false).',
            'is_active.boolean' => 'قيمة التفعيل يجب أن تكون صحيحة (true/false).',
        ];
    }

    /**
     * 🧠 تحقق إضافي بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // لا يمكن توثيق المورد بدون بيانات اتصال
            if ($this->is_verified && empty($this->contact_email) && empty($this->contact_phone)) {
                $validator->errors()->add('is_verified', 'لا يمكن توثيق المورد بدون بيانات تواصل (بريد أو هاتف).');
            }
        });
    }
}
