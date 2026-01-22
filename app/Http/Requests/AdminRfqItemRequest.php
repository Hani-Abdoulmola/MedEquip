<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRfqItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('rfq')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'specifications' => 'nullable|string|max:5000',
            'quantity' => 'required|integer|min:1|max:999999',
            'unit' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.exists' => 'المنتج المحدد غير موجود.',
            'item_name.required' => 'اسم البند مطلوب.',
            'item_name.max' => 'اسم البند يجب ألا يتجاوز 200 حرف.',
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'quantity.min' => 'الكمية يجب أن تكون على الأقل 1.',
            'quantity.max' => 'الكمية يجب ألا تتجاوز 999999.',
            'specifications.max' => 'المواصفات يجب ألا تتجاوز 5000 حرف.',
            'unit.max' => 'الوحدة يجب ألا تتجاوز 50 حرف.',
        ];
    }
}
