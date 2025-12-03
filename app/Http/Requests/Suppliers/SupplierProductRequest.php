<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تتحقق في الـController عبر role Supplier
    }

    /**
     * تحديد هل الطلب Save OR Update
     */
    public function isUpdate(): bool
    {
        return in_array($this->method(), ['PUT', 'PATCH']);
    }

    public function rules(): array
    {
        $update = $this->isUpdate();

        return [
            // ———————————————— 🟦 مشتركة
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'lead_time' => ['nullable', 'string', 'max:100'],
            'warranty' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['available', 'out_of_stock', 'suspended'])],
            'notes' => ['nullable', 'string', 'max:1000'],

            'specifications' => ['nullable', 'array'],
            'features' => ['nullable', 'array'],
            'technical_data' => ['nullable', 'array'],
            'certifications' => ['nullable', 'array'],
            'installation_requirements' => ['nullable', 'string'],

            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5024'],

            // ———————————————— 🟩 إذا كان Update
            'name' => [$update ? 'required' : 'nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:6000'],

            // ———————————————— 🟧 إذا كان إنشاء new
            'action' => $update ? 'nullable' : 'required|in:new,existing',
            'product_id' => $update ? 'nullable' : 'required_if:action,existing|exists:products,id',
            'name' => $update ? 'required|max:255' : 'required_if:action,new|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المنتج مطلوب',
            'price.required' => 'السعر مطلوب',
            'stock_quantity.required' => 'الكمية مطلوبة',
            'action.required' => 'يجب تحديد نوع العملية (منتج جديد أو منتج موجود)',
            'product_id.required_if' => 'الرجاء تحديد منتج موجود',
        ];
    }
}
