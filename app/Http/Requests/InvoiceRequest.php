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
            'total_amount' => 'nullable|numeric|min:0', // Made optional - will be auto-calculated
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
            'status.in' => 'حالة الفاتورة غير صحيحة.',
            'payment_status.in' => 'حالة الدفع غير صحيحة.',
        ];
    }

    /**
     * Add custom validation for total_amount calculation
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $subtotal = (float) ($this->input('subtotal', 0));
            $tax = (float) ($this->input('tax', 0));
            $discount = (float) ($this->input('discount', 0));
            $totalAmount = $this->input('total_amount');

            // Validate discount doesn't exceed subtotal
            if ($discount > $subtotal) {
                $validator->errors()->add(
                    'discount',
                    'الخصم لا يمكن أن يتجاوز المجموع الفرعي'
                );
            }

            // Validate tax is reasonable (0-100%)
            if ($tax < 0) {
                $validator->errors()->add(
                    'tax',
                    'الضريبة لا يمكن أن تكون سالبة'
                );
            }

            // Calculate expected total
            $calculated = max(0, $subtotal + $tax - $discount);

            // If total_amount is provided, validate it matches calculation (with tolerance for rounding)
            if ($totalAmount !== null) {
                $totalAmountFloat = (float) $totalAmount;
                // Allow small rounding differences (0.01)
                if (abs($totalAmountFloat - $calculated) > 0.01) {
                    $validator->errors()->add(
                        'total_amount',
                        'المجموع الكلي يجب أن يساوي (المجموع الفرعي + الضريبة - الخصم). القيمة المحسوبة: ' . number_format($calculated, 2)
                    );
                }
            }
        });
    }
}
