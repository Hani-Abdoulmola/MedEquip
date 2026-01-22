<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyerCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Buyer') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        // Different rules for different actions
        if ($action === 'add' || $action === 'update') {
            return [
                'quantity' => 'required|integer|min:1|max:10000',
                'specifications' => 'nullable|string|max:1000',
                'unit' => 'nullable|string|max:50',
            ];
        }

        if ($action === 'submitRfq') {
            return [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'deadline' => 'nullable|date|after:today',
                'supplier_ids' => 'nullable|array',
                'supplier_ids.*' => 'exists:suppliers,id',
            ];
        }

        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'quantity.min' => 'الكمية يجب أن تكون على الأقل 1.',
            'quantity.max' => 'الكمية يجب ألا تتجاوز 10000.',
            'specifications.max' => 'المواصفات يجب ألا تتجاوز 1000 حرف.',
            'unit.max' => 'الوحدة يجب ألا تتجاوز 50 حرف.',
            'title.required' => 'عنوان طلب عرض السعر مطلوب.',
            'title.max' => 'العنوان يجب ألا يتجاوز 255 حرف.',
            'description.max' => 'الوصف يجب ألا يتجاوز 2000 حرف.',
            'deadline.date' => 'تاريخ الاستحقاق غير صحيح.',
            'deadline.after' => 'تاريخ الاستحقاق يجب أن يكون بعد اليوم.',
            'supplier_ids.array' => 'الموردون يجب أن يكونوا مصفوفة.',
            'supplier_ids.*.exists' => 'أحد الموردين المحددين غير موجود.',
        ];
    }
}
