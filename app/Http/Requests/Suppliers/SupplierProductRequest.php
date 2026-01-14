<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user() && $this->user()->supplierProfile);
    }

    /**
     * Is this an update request?
     */
    public function isUpdate(): bool
    {
        return in_array($this->method(), ['PUT', 'PATCH']);
    }

    public function rules(): array
    {
        $update = $this->isUpdate();
        $product = $this->route('product');

        /**
         * Common rules (Offer/Pivot Data)
         */
        $rules = [
            'price'          => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'lead_time'      => ['nullable', 'string', 'max:100'],
            'warranty'       => ['nullable', 'string', 'max:100'],
            'status'         => ['required', Rule::in(['available', 'out_of_stock', 'suspended'])],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ];

        /**
         * UPDATE: Can update pivot data always
         * Can update product data ONLY if review_status = 'needs_update'
         */
        if ($update) {
            $canUpdateBaseProduct = $product && $product->review_status === Product::REVIEW_NEEDS_UPDATE;

            if ($canUpdateBaseProduct) {
                $rules = array_merge($rules, [
                    'name'            => ['required', 'string', 'max:255'],
                    'model'           => ['nullable', 'string', 'max:100'],
                    'brand'           => ['nullable', 'string', 'max:100'],
                    'category_id'     => ['required', 'exists:product_categories,id'],
                    'manufacturer_id' => ['nullable', Rule::exists('manufacturers', 'id')->where('is_active', true)],
                    'description'     => ['nullable', 'string', 'max:6000'],
                    'specifications'  => ['nullable', 'string', 'max:6000'],
                    'features'        => ['nullable', 'string', 'max:6000'],
                    'technical_data'  => ['nullable', 'string', 'max:6000'],
                    'certifications'  => ['nullable', 'string', 'max:6000'],
                    'installation_requirements' => ['nullable', 'string', 'max:5000'],
                    'images'   => ['nullable', 'array'],
                    'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                ]);
            }

            return $rules;
        }

        /**
         * STORE: Must specify action type
         */
        $rules['action'] = ['required', Rule::in(['new', 'existing'])];

        /**
         * Conditionally apply rules based on action
         */
        $action = $this->input('action');

        if ($action === 'new') {
            // NEW PRODUCT rules
            $rules = array_merge($rules, [
                'name'            => ['required', 'string', 'max:255'],
                'model'           => ['nullable', 'string', 'max:100'],
                'brand'           => ['nullable', 'string', 'max:100'],
                'category_id'     => ['required', 'exists:product_categories,id'],
                'manufacturer_id' => ['nullable', Rule::exists('manufacturers', 'id')->where('is_active', true)],
                'description'     => ['nullable', 'string', 'max:6000'],
                'specifications'  => ['nullable', 'string', 'max:6000'],
                'features'        => ['nullable', 'string', 'max:6000'],
                'technical_data'  => ['nullable', 'string', 'max:6000'],
                'certifications'  => ['nullable', 'string', 'max:6000'],
                'installation_requirements' => ['nullable', 'string', 'max:5000'],
                'images'   => ['nullable', 'array'],
                'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ]);
        } else {
            // EXISTING PRODUCT rules
            $rules['product_id'] = ['required', Rule::exists('products', 'id')->where('is_active', true)];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Action
            'action.required' => 'يجب اختيار نوع العملية.',
            'action.in'       => 'نوع العملية غير صالح.',

            // Product fields
            'name.required'    => 'اسم المنتج مطلوب.',
            'name.string'      => 'اسم المنتج يجب أن يكون نصاً.',
            'name.max'         => 'اسم المنتج يجب ألا يتجاوز 255 حرفاً.',

            'model.max' => 'الموديل يجب ألا يتجاوز 100 حرف.',
            'brand.max' => 'العلامة التجارية يجب ألا تتجاوز 100 حرف.',

            'category_id.required' => 'يجب اختيار فئة المنتج.',
            'category_id.exists'   => 'الفئة المختارة غير موجودة.',

            'manufacturer_id.exists' => 'الشركة المصنعة غير موجودة أو غير نشطة.',

            'description.max'    => 'الوصف يجب ألا يتجاوز 6000 حرف.',
            'specifications.max' => 'المواصفات يجب ألا تتجاوز 6000 حرف.',
            'features.max'       => 'المميزات يجب ألا تتجاوز 6000 حرف.',

            // Existing product
            'product_id.required' => 'يجب اختيار منتج من الكتالوج.',
            'product_id.exists'   => 'المنتج المختار غير متاح.',

            // Offer data
            'price.required'          => 'السعر مطلوب.',
            'price.numeric'           => 'السعر يجب أن يكون رقماً.',
            'price.min'               => 'السعر يجب أن يكون 0 أو أكثر.',

            'stock_quantity.required' => 'الكمية مطلوبة.',
            'stock_quantity.integer'  => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'stock_quantity.min'      => 'الكمية يجب أن تكون 0 أو أكثر.',

            'lead_time.max' => 'مدة التوصيل يجب ألا تتجاوز 100 حرف.',
            'warranty.max'  => 'الضمان يجب ألا يتجاوز 100 حرف.',

            'status.required' => 'يجب اختيار حالة المنتج.',
            'status.in'       => 'حالة المنتج غير صالحة.',

            'notes.max' => 'الملاحظات يجب ألا تتجاوز 2000 حرف.',

            // Images
            'images.array'   => 'الصور يجب أن تكون مصفوفة.',
            'images.*.image' => 'الملف يجب أن يكون صورة.',
            'images.*.mimes' => 'صيغة الصورة يجب أن تكون JPG أو PNG أو WEBP.',
            'images.*.max'   => 'حجم الصورة يجب ألا يتجاوز 5MB.',
        ];
    }
}
