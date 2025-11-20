<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار بالـ middleware
    }

    public function rules(): array
    {
        $id = $this->route('order')?->id;

        return [
            'quotation_id' => 'required|exists:quotations,id',
            'buyer_id' => 'required|exists:buyers,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'currency' => [
                'required',
                'string',
                Rule::in([
                    Order::CURRENCY_LYD,
                    Order::CURRENCY_USD,
                    Order::CURRENCY_EUR,
                ]),
            ],
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'quotation_id.required' => 'العرض المرتبط مطلوب.',
            'quotation_id.exists' => 'العرض المحدد غير موجود.',
            'buyer_id.required' => 'المشتري مطلوب.',
            'buyer_id.exists' => 'المشتري المحدد غير موجود.',
            'supplier_id.required' => 'المورد مطلوب.',
            'supplier_id.exists' => 'المورد المحدد غير موجود.',
            'status.in' => 'حالة الطلب غير صحيحة.',
            'total_amount.required' => 'القيمة الإجمالية مطلوبة.',
            'currency.required' => 'العملة مطلوبة.',
            'currency.in' => 'العملة المحددة غير مدعومة. العملات المتاحة: LYD, USD, EUR.',
        ];
    }
}
