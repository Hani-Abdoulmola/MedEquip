<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user() && $this->user()->supplierProfile);
    }

    /**
     * هل الطلب Update ؟
     */
    public function isUpdate(): bool
    {
        return in_array($this->method(), ['PUT', 'PATCH']);
    }

    public function rules(): array
    {
        $update = $this->isUpdate();

        /**
         * 🟦 قواعد مشتركة (Pivot Data - عرض المورد)
         */
        $rules = [
            'price'          => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'lead_time'      => ['nullable', 'string', 'max:100'],
            'warranty'       => ['nullable', 'string', 'max:100'],
            'status'         => ['required', Rule::in(['available', 'out_of_stock', 'suspended'])],
            'notes'          => ['nullable', 'string', 'max:2000'],

            // Text / JSON-like fields
            'specifications'            => ['nullable', 'string', 'max:6000'],
            'features'                  => ['nullable', 'string', 'max:6000'],
            'technical_data'            => ['nullable', 'string', 'max:6000'],
            'certifications'            => ['nullable', 'string', 'max:6000'],
            'installation_requirements' => ['nullable', 'string', 'max:5000'],

            // صور المنتج
            'images'   => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        /**
         * 🟩 Update → تحديث Base Product موجود
         */
        if ($update) {
            return array_merge($rules, [
                'name'            => ['required', 'string', 'max:255'],
                'model'           => ['nullable', 'string', 'max:100'],
                'brand'           => ['nullable', 'string', 'max:100'],
                'category_id'     => ['nullable', 'exists:product_categories,id'],
                'manufacturer_id' => ['nullable', 'exists:manufacturers,id'],
                'description'     => ['nullable', 'string', 'max:6000'],
            ]);
        }

        /**
         * 🟧 Store → لازم نحدد نوع العملية
         */
        $rules['action'] = ['required', Rule::in(['new', 'existing'])];

        /**
         * 🆕 إنشاء منتج جديد
         */
        $rulesNew = [
            'name'            => ['required_if:action,new', 'string', 'max:255'],
            'model'           => ['nullable', 'string', 'max:100'],
            'brand'           => ['nullable', 'string', 'max:100'],
            'category_id'     => ['required_if:action,new', 'exists:product_categories,id'],
            'manufacturer_id' => ['nullable', 'exists:manufacturers,id'],
            'description'     => ['nullable', 'string', 'max:6000'],
        ];

        /**
         * 🔗 ربط منتج موجود
         * CRITICAL FIX: Added 'nullable' to prevent validation failure when action='new'
         */
        $rulesExisting = [
            'product_id' => [
                'required_if:action,existing',
                'nullable',
                Rule::exists('products', 'id')
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],
        ];

        return array_merge($rules, $rulesNew, $rulesExisting);
    }

    public function messages(): array
    {
        return [
            'action.required'         => 'يجب اختيار نوع العملية (إضافة أو ربط منتج).',
            'action.in'               => 'نوع العملية غير صالح.',

            'name.required_if'        => 'اسم المنتج مطلوب عند إنشاء منتج جديد.',
            'category_id.required_if' => 'يجب اختيار فئة المنتج.',
            'product_id.required_if'  => 'يجب اختيار منتج موجود في حالة الربط.',
            'product_id.exists'       => 'المنتج المختار غير صالح أو مرتبط بك مسبقاً.',

            'price.required'          => 'السعر مطلوب.',
            'stock_quantity.required' => 'الكمية مطلوبة.',

            'images.*.mimes'          => 'يجب أن تكون الصورة بصيغة JPG أو JPEG أو PNG أو WEBP.',
            'images.*.max'            => 'الحد الأقصى لحجم الصورة 5MB.',
        ];
    }
}

