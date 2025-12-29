<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;

class SupplierDeliveryRequest extends FormRequest
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
            'delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'delivery_location' => ['required', 'string', 'max:255'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'delivery_date.required' => 'تاريخ التسليم مطلوب.',
            'delivery_date.date' => 'يجب إدخال تاريخ صحيح.',
            'delivery_date.after_or_equal' => 'تاريخ التسليم يجب أن يكون اليوم أو في المستقبل.',
            'delivery_location.required' => 'موقع التسليم مطلوب.',
            'delivery_location.string' => 'موقع التسليم يجب أن يكون نصاً.',
            'delivery_location.max' => 'موقع التسليم لا يمكن أن يتجاوز 255 حرفاً.',
            'receiver_name.required' => 'اسم المستلم مطلوب.',
            'receiver_name.string' => 'اسم المستلم يجب أن يكون نصاً.',
            'receiver_name.max' => 'اسم المستلم لا يمكن أن يتجاوز 255 حرفاً.',
            'receiver_phone.required' => 'هاتف المستلم مطلوب.',
            'receiver_phone.string' => 'هاتف المستلم يجب أن يكون نصاً.',
            'receiver_phone.max' => 'هاتف المستلم لا يمكن أن يتجاوز 50 حرفاً.',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً.',
            'notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف.',
        ];
    }
}

