<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحكم في الصلاحيات عبر Middleware
    }

    public function rules(): array
    {
        $id = $this->route('invoice')?->id;

        return [
            'order_id' => 'required|exists:orders,id',
            'invoice_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,issued,approved,cancelled',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'الطلب المرتبط بالفاتورة مطلوب.',
            'order_id.exists' => 'الطلب المحدد غير موجود.',
            'subtotal.required' => 'المجموع الفرعي مطلوب.',
            'total_amount.required' => 'المجموع الكلي مطلوب.',
            'status.in' => 'حالة الفاتورة غير صحيحة.',
            'payment_status.in' => 'حالة الدفع غير صحيحة.',
        ];
    }
}
