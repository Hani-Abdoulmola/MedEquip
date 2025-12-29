<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManufacturerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحكم في الصلاحيات يتم عبر Middleware
    }

    public function rules(): array
    {
        $id = $this->route('manufacturer')?->id;
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('manufacturers', 'slug')->ignore($id)],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'country' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الشركة المصنعة مطلوب.',
            'name.string' => 'اسم الشركة المصنعة يجب أن يكون نصًا.',
            'name.max' => 'اسم الشركة المصنعة لا يمكن أن يتجاوز 255 حرفًا.',
            'name_ar.string' => 'الاسم بالعربية يجب أن يكون نصًا.',
            'name_ar.max' => 'الاسم بالعربية لا يمكن أن يتجاوز 255 حرفًا.',
            'slug.unique' => 'الرابط المخصص مستخدم بالفعل.',
            'category_id.exists' => 'الفئة المحددة غير موجودة.',
            'country.max' => 'اسم الدولة لا يمكن أن يتجاوز 100 حرف.',
            'website.url' => 'الموقع الإلكتروني يجب أن يكون رابطًا صحيحًا.',
            'website.max' => 'الموقع الإلكتروني لا يمكن أن يتجاوز 255 حرفًا.',
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون نعم أو لا.',
        ];
    }
}

