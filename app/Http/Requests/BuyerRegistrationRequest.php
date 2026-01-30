<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/**
 * 📝 طلب التحقق من بيانات تسجيل المشتري (Buyer Registration)
 * 
 * يتحقق من بيانات المستخدم + بيانات المشتري معًا في عملية التسجيل
 */
class BuyerRegistrationRequest extends FormRequest
{
    /**
     * 🔐 التحقق من الصلاحيات
     */
    public function authorize(): bool
    {
        return true; // التسجيل متاح للجميع
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->has('email') ? strtolower(trim($this->email)) : null,
            'contact_email' => $this->has('contact_email') && $this->contact_email ? strtolower(trim($this->contact_email)) : null,
        ]);
    }

    /**
     * 📋 قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            // 👤 بيانات المستخدم الأساسية
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // 🏢 بيانات المشتري (المؤسسة الصحية)
            'organization_name' => ['required', 'string', 'max:200'],
            'organization_type' => ['required', 'string', 'max:100', 'in:مستشفى,عيادة,مختبر,مركز طبي,صيدلية,أخرى'],
            'license_number' => ['required', 'string', 'max:100'],
            'license_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            // 🌍 الموقع والاتصال
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
        ];
    }

    /**
     * 📝 رسائل الأخطاء المخصصة
     */
    public function messages(): array
    {
        return [
            // رسائل بيانات المستخدم
            'name.required' => 'الاسم الكامل مطلوب.',
            'name.max' => 'الاسم لا يمكن أن يتجاوز 255 حرفًا.',
            
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',
            
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',

            // رسائل بيانات المشتري
            'organization_name.required' => 'اسم المؤسسة الصحية مطلوب.',
            'organization_name.max' => 'اسم المؤسسة لا يمكن أن يتجاوز 200 حرف.',
            
            'organization_type.required' => 'نوع المؤسسة مطلوب.',
            'organization_type.in' => 'نوع المؤسسة غير صحيح.',

            'license_number.required' => 'رقم الترخيص الصحي مطلوب.',
            'license_number.max' => 'رقم الترخيص لا يمكن أن يتجاوز 100 حرف.',

            'license_document.required' => 'رفع وثيقة الترخيص مطلوب.',
            'license_document.file' => 'يجب رفع ملف وثيقة الترخيص.',
            'license_document.mimes' => 'وثيقة الترخيص يجب أن تكون من نوع PDF أو JPG أو PNG.',
            'license_document.max' => 'حجم ملف وثيقة الترخيص يجب ألا يتجاوز 5 ميجابايت.',
            
            'country.required' => 'الدولة مطلوبة.',
            
            'contact_email.email' => 'صيغة البريد الإلكتروني للتواصل غير صحيحة.',
            'contact_phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',
        ];
    }

    /**
     * 🏷️ أسماء الحقول المخصصة
     */
    public function attributes(): array
    {
        return [
            'name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'organization_name' => 'اسم المؤسسة',
            'organization_type' => 'نوع المؤسسة',
            'license_number' => 'رقم الترخيص',
            'country' => 'الدولة',
            'city' => 'المدينة',
            'address' => 'العنوان',
            'contact_email' => 'البريد الإلكتروني للتواصل',
            'contact_phone' => 'رقم الهاتف للتواصل',
        ];
    }
}

