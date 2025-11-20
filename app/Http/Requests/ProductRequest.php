<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 🔐 التحكم في الوصول يتم عبر Middleware (Spatie Permissions)
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);

        return [
            // 🧾 بيانات المنتج العامة
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],

            // ⚙️ حالة التفعيل
            'is_active' => ['boolean'],

            // 🖼️ الصورة (باستخدام Spatie MediaLibrary)
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            // البيانات الأساسية
            'name.required' => 'اسم المنتج مطلوب.',
            'name.sometimes' => 'اسم المنتج مطلوب.',
            'name.string' => 'اسم المنتج يجب أن يكون نصًا.',
            'name.max' => 'اسم المنتج لا يمكن أن يتجاوز 255 حرفًا.',

            'model.string' => 'رقم الموديل يجب أن يكون نصًا.',
            'model.max' => 'رقم الموديل لا يمكن أن يتجاوز 100 حرف.',

            'brand.string' => 'العلامة التجارية يجب أن تكون نصًا.',
            'brand.max' => 'العلامة التجارية لا يمكن أن تتجاوز 100 حرف.',

            'description.string' => 'الوصف يجب أن يكون نصًا.',
            'description.max' => 'الوصف لا يمكن أن يتجاوز 5000 حرف.',

            // الفئة
            'category_id.exists' => 'الفئة المحددة غير موجودة.',

            // حالة التفعيل
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون نعم أو لا.',

            // الصورة
            'image.image' => 'يجب أن يكون الملف صورة.',
            'image.mimes' => 'الأنواع المسموح بها: jpg, jpeg, png, webp.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }
}
