<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تدار عبر Middleware و Spatie Permission
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);

        return [
            // 🧩 نوع المستخدم (مشتري / مورد / إداري)
            'user_type_id' => ['required', 'exists:user_types,id'],

            // 👤 الاسم الكامل
            'name' => ['required', 'string', 'max:255'],

            // 📧 البريد الإلكتروني
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],

            // 📱 رقم الهاتف (اختياري لكنه يجب أن يكون فريدًا)
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($id),
                'regex:/^[0-9+\-\s()]+$/',
            ],

            // 🔐 كلمة المرور
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', // تحتوي على رقم وحرف على الأقل
            ],

            // ⚙️ الحالة التشغيلية
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],

            // 🧠 الدور (Spatie Role)
            'role' => ['nullable', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_type_id.required' => 'يجب تحديد نوع المستخدم.',
            'user_type_id.exists' => 'نوع المستخدم المحدد غير موجود في النظام.',

            'name.required' => 'الاسم مطلوب.',
            'name.max' => 'الاسم لا يمكن أن يتجاوز 255 حرفًا.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',

            'phone.unique' => 'رقم الهاتف مستخدم مسبقًا.',
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل.',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على أحرف وأرقام على الأقل.',

            'status.required' => 'يجب تحديد حالة المستخدم.',
            'status.in' => 'قيمة الحالة غير صحيحة.',

            'role.exists' => 'الدور المحدد غير موجود في النظام.',
        ];
    }

    /**
     * ✅ تحقق إضافي بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // لا يمكن تعطيل مستخدم إداري أساسي
            if ($this->status === 'inactive' && $this->user_type_id == 1) {
                $validator->errors()->add('status', 'لا يمكن تعطيل حساب إداري أساسي.');
            }
        });
    }
}
