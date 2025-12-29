<?php

namespace App\Http\Requests\Suppliers;

use App\Models\RfqItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user() && $this->user()->supplierProfile);
    }

    public function rules(): array
    {
        $rfqId = $this->route('rfq')?->id;

        return [
            'total_price' => ['required', 'numeric', 'min:0'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'valid_until' => ['required', 'date', 'after:today'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'], // 10MB
            
            // Items array validation
            'items' => ['nullable', 'array'],
            'items.*.rfq_item_id' => [
                'required',
                'exists:rfq_items,id',
                function ($attribute, $value, $fail) use ($rfqId) {
                    if ($rfqId) {
                        $rfqItem = RfqItem::find($value);
                        if ($rfqItem && $rfqItem->rfq_id != $rfqId) {
                            $fail('البند لا ينتمي إلى هذا الطلب.');
                        }
                    }
                },
            ],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'items.*.lead_time' => ['nullable', 'string', 'max:100'],
            'items.*.warranty' => ['nullable', 'string', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_price.required' => 'السعر الإجمالي مطلوب.',
            'total_price.numeric' => 'السعر يجب أن يكون رقماً.',
            'total_price.min' => 'السعر لا يمكن أن يكون سالباً.',
            'valid_until.required' => 'تاريخ انتهاء صلاحية العرض مطلوب.',
            'valid_until.date' => 'يجب إدخال تاريخ صحيح.',
            'valid_until.after' => 'تاريخ انتهاء الصلاحية يجب أن يكون في المستقبل.',
            'attachments.*.mimes' => 'المرفقات يجب أن تكون بصيغة PDF أو Word أو صور.',
            'attachments.*.max' => 'حجم المرفق لا يمكن أن يتجاوز 10 ميجابايت.',
            'items.array' => 'عناصر العرض يجب أن تكون مصفوفة.',
            'items.*.rfq_item_id.required' => 'معرف بند الطلب مطلوب.',
            'items.*.rfq_item_id.exists' => 'بند الطلب المحدد غير موجود.',
            'items.*.unit_price.required' => 'سعر الوحدة مطلوب.',
            'items.*.unit_price.numeric' => 'سعر الوحدة يجب أن يكون رقماً.',
            'items.*.unit_price.min' => 'سعر الوحدة لا يمكن أن يكون سالباً.',
            'items.*.unit_price.max' => 'سعر الوحدة مرتفع جداً.',
            'items.*.lead_time.max' => 'مدة التوصيل لا يمكن أن تتجاوز 100 حرف.',
            'items.*.warranty.max' => 'الضمان لا يمكن أن يتجاوز 100 حرف.',
            'items.*.notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف.',
        ];
    }
}

