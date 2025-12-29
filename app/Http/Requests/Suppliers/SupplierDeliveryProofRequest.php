<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;

class SupplierDeliveryProofRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller
        return (bool) ($this->user() && $this->user()->supplierProfile);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'], // 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'proof.required' => 'يرجى اختيار ملف إثبات التسليم.',
            'proof.file' => 'يجب أن يكون الملف صحيحاً.',
            'proof.mimes' => 'يجب أن يكون الملف من نوع: jpg, jpeg, png, pdf.',
            'proof.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت.',
        ];
    }
}

