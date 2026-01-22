<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyerReviewRequest extends FormRequest
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
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'nullable|string|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'would_recommend' => 'boolean',
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
            'supplier_id.required' => 'يرجى اختيار المورد.',
            'supplier_id.exists' => 'المورد المحدد غير موجود.',
            'order_id.exists' => 'الطلب المحدد غير موجود.',
            'overall_rating.required' => 'التقييم العام مطلوب.',
            'overall_rating.integer' => 'التقييم العام يجب أن يكون رقماً صحيحاً.',
            'overall_rating.min' => 'الحد الأدنى للتقييم هو 1.',
            'overall_rating.max' => 'الحد الأقصى للتقييم هو 5.',
            'quality_rating.min' => 'تقييم الجودة يجب أن يكون بين 1 و 5.',
            'quality_rating.max' => 'تقييم الجودة يجب أن يكون بين 1 و 5.',
            'communication_rating.min' => 'تقييم التواصل يجب أن يكون بين 1 و 5.',
            'communication_rating.max' => 'تقييم التواصل يجب أن يكون بين 1 و 5.',
            'delivery_rating.min' => 'تقييم التوصيل يجب أن يكون بين 1 و 5.',
            'delivery_rating.max' => 'تقييم التوصيل يجب أن يكون بين 1 و 5.',
            'value_rating.min' => 'تقييم القيمة يجب أن يكون بين 1 و 5.',
            'value_rating.max' => 'تقييم القيمة يجب أن يكون بين 1 و 5.',
            'title.max' => 'العنوان يجب ألا يتجاوز 255 حرف.',
            'review.max' => 'المراجعة يجب ألا تتجاوز 2000 حرف.',
            'pros.max' => 'الإيجابيات يجب ألا تتجاوز 500 حرف.',
            'cons.max' => 'السلبيات يجب ألا تتجاوز 500 حرف.',
            'would_recommend.boolean' => 'حقل التوصية يجب أن يكون نعم أو لا.',
        ];
    }
}
