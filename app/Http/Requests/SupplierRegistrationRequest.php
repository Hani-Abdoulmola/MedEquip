<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/**
 * 📝 طلب التحقق من بيانات تسجيل المورد (Supplier Registration)
 * 
 * يتحقق من بيانات المستخدم + بيانات المورد معًا في عملية التسجيل
 */
class SupplierRegistrationRequest extends FormRequest
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

            // 🏢 بيانات المورد (الشركة)
            'company_name' => ['required', 'string', 'max:200', 'unique:suppliers,company_name'],
            'commercial_register' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'verification_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

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
            'name.string' => 'الاسم يجب أن يكون نصًا صالحًا.',
            'name.max' => 'الاسم لا يمكن أن يتجاوز 255 حرفًا.',
            
            'email.required' => 'البريد الإلكتروني (حساب الدخول) مطلوب.',
            'email.string' => 'البريد الإلكتروني يجب أن يكون نصًا صالحًا.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.max' => 'البريد الإلكتروني لا يمكن أن يتجاوز 255 حرفًا.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            
            'phone.string' => 'رقم الهاتف يجب أن يكون نصًا صالحًا.',
            'phone.max' => 'رقم الهاتف لا يمكن أن يتجاوز 50 حرفًا.',
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',
            
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
            'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل.',

            // رسائل بيانات المورد
            'company_name.required' => 'اسم الشركة مطلوب.',
            'company_name.string' => 'اسم الشركة يجب أن يكون نصًا صالحًا.',
            'company_name.max' => 'اسم الشركة لا يمكن أن يتجاوز 200 حرف.',
            'company_name.unique' => 'يوجد مورد بنفس اسم الشركة.',

            'commercial_register.required' => 'رقم السجل التجاري / رقم الترخيص مطلوب.',
            'commercial_register.string' => 'رقم السجل التجاري يجب أن يكون نصًا صالحًا.',
            'commercial_register.max' => 'رقم السجل التجاري لا يمكن أن يتجاوز 100 حرف.',

            'tax_number.string' => 'الرقم الضريبي يجب أن يكون نصًا صالحًا.',
            'tax_number.max' => 'الرقم الضريبي لا يمكن أن يتجاوز 100 حرف.',

            'verification_document.required' => 'رفع وثيقة التحقق (رخصة أو سجل تجاري) مطلوب.',
            'verification_document.file' => 'يجب رفع ملف وثيقة التحقق.',
            'verification_document.mimes' => 'وثيقة التحقق يجب أن تكون من نوع PDF أو JPG أو PNG.',
            'verification_document.max' => 'حجم ملف وثيقة التحقق يجب ألا يتجاوز 5 ميجابايت.',
            
            'country.required' => 'الدولة مطلوبة.',
            'country.string' => 'الدولة يجب أن تكون نصًا صالحًا.',
            'country.max' => 'الدولة لا يمكن أن تتجاوز 100 حرف.',

            'city.string' => 'المدينة يجب أن تكون نصًا صالحًا.',
            'city.max' => 'المدينة لا يمكن أن تتجاوز 100 حرف.',

            'address.string' => 'العنوان يجب أن يكون نصًا صالحًا.',
            'address.max' => 'العنوان لا يمكن أن يتجاوز 255 حرفًا.',
            
            'contact_email.email' => 'صيغة البريد الإلكتروني للتواصل غير صحيحة.',
            'contact_email.max' => 'البريد الإلكتروني للتواصل لا يمكن أن يتجاوز 150 حرفًا.',
            'contact_phone.string' => 'رقم الهاتف للتواصل يجب أن يكون نصًا صالحًا.',
            'contact_phone.max' => 'رقم الهاتف للتواصل لا يمكن أن يتجاوز 50 حرفًا.',
            'contact_phone.regex' => 'صيغة رقم الهاتف للتواصل غير صحيحة.',
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
            'company_name' => 'اسم الشركة',
            'commercial_register' => 'رقم السجل التجاري',
            'tax_number' => 'الرقم الضريبي',
            'verification_document' => 'وثيقة التحقق',
            'country' => 'الدولة',
            'city' => 'المدينة',
            'address' => 'العنوان',
            'contact_email' => 'البريد الإلكتروني للتواصل',
            'contact_phone' => 'رقم الهاتف للتواصل',
        ];
    }
}

