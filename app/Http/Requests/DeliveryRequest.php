<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Spatie Permissions تتحكم في الوصول
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'buyer_id' => 'required|exists:buyers,id',
            'delivery_date' => 'required|date',
            'delivery_location' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,shipped,delivered,cancelled',
            'is_verified' => 'boolean',
            'verified_by' => 'nullable|exists:users,id',
            'verified_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'يجب اختيار الطلب المرتبط بالتسليم.',
            'supplier_id.required' => 'يجب تحديد المورد.',
            'buyer_id.required' => 'يجب تحديد المشتري.',
            'delivery_date.required' => 'تاريخ التسليم مطلوب.',
            'delivery_location.required' => 'موقع التسليم مطلوب.',
            'receiver_name.required' => 'اسم المستلم مطلوب.',
            'status.in' => 'حالة التسليم غير صحيحة.',
        ];
    }
}
