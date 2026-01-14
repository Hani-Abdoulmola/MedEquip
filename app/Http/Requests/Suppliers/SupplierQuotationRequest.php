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
            
            // Items array validation - REQUIRED, must match all RFQ items
            'items' => ['required', 'array', 'min:1'],
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
            'items.*.quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    // Extract index from attribute using regex (e.g., "items.0.quantity" -> 0, "items.10.quantity" -> 10)
                    preg_match('/items\.(\d+)\.quantity/', $attribute, $matches);
                    $itemIndex = $matches[1] ?? null;
                    
                    if ($itemIndex !== null) {
                        $rfqItemId = $this->input("items.{$itemIndex}.rfq_item_id");
                        
                        if ($rfqItemId) {
                            $rfqItem = RfqItem::find($rfqItemId);
                            if ($rfqItem && $value != $rfqItem->quantity) {
                                $fail('الكمية يجب أن تطابق كمية الطلب (' . $rfqItem->quantity . ').');
                            }
                        }
                    }
                },
            ],
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
            'items.required' => 'يجب تقديم سعر لجميع بنود الطلب.',
            'items.min' => 'يجب تقديم سعر لجميع بنود الطلب.',
            'items.array' => 'عناصر العرض يجب أن تكون مصفوفة.',
            'items.*.rfq_item_id.required' => 'معرف بند الطلب مطلوب.',
            'items.*.rfq_item_id.exists' => 'بند الطلب المحدد غير موجود.',
            'items.*.unit_price.required' => 'سعر الوحدة مطلوب.',
            'items.*.unit_price.numeric' => 'سعر الوحدة يجب أن يكون رقماً.',
            'items.*.unit_price.min' => 'سعر الوحدة لا يمكن أن يكون سالباً.',
            'items.*.unit_price.max' => 'سعر الوحدة مرتفع جداً.',
            'items.*.quantity.required' => 'الكمية مطلوبة.',
            'items.*.quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'items.*.lead_time.max' => 'مدة التوصيل لا يمكن أن تتجاوز 100 حرف.',
            'items.*.warranty.max' => 'الضمان لا يمكن أن يتجاوز 100 حرف.',
            'items.*.notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف.',
        ];
    }

    /**
     * Additional validation after standard rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rfqId = $this->route('rfq')?->id;
            $items = $this->input('items', []);
            $totalPrice = floatval($this->input('total_price', 0));

            if ($rfqId) {
                $rfq = \App\Models\Rfq::with('items')->find($rfqId);
                
                if ($rfq) {
                    // Check RFQ status - must be 'open' to submit quotation
                    if ($rfq->status !== 'open') {
                        $validator->errors()->add('rfq_id', 'هذا الطلب لم يعد مفتوحًا لتقديم العروض.');
                    }
                    
                    // Check deadline - must not be in the past
                    if ($rfq->deadline && $rfq->deadline->isPast()) {
                        $validator->errors()->add('rfq_id', 'انتهت فترة تقديم العروض لهذا الطلب.');
                    }

                    // Validate that all RFQ items are quoted
                    if (!empty($items)) {
                        $rfqItemIds = $rfq->items->pluck('id')->toArray();
                        $quotedItemIds = collect($items)->pluck('rfq_item_id')->toArray();
                        $missingItems = array_diff($rfqItemIds, $quotedItemIds);
                        
                        if (count($missingItems) > 0) {
                            $validator->errors()->add(
                                'items',
                                'يجب تقديم سعر لجميع بنود الطلب. البنود المفقودة: ' . count($missingItems)
                            );
                        }

                        // Validate that each rfq_item_id appears only once (no duplicates)
                        $duplicates = array_diff_assoc($quotedItemIds, array_unique($quotedItemIds));
                        if (count($duplicates) > 0) {
                            $validator->errors()->add(
                                'items',
                                'لا يمكن تقديم سعر لنفس البند مرتين.'
                            );
                        }

                        // Validate that total price matches sum of items
                        $calculatedTotal = 0;
                        foreach ($items as $item) {
                            $rfqItem = RfqItem::find($item['rfq_item_id']);
                            if ($rfqItem && !empty($item['unit_price'])) {
                                $calculatedTotal += floatval($item['unit_price']) * $rfqItem->quantity;
                            }
                        }
                        
                        $tolerance = 0.01; // Allow 1 cent difference for rounding
                        if ($calculatedTotal > 0 && abs($calculatedTotal - $totalPrice) > $tolerance) {
                            $validator->errors()->add(
                                'total_price',
                                "السعر الإجمالي ({$totalPrice}) لا يطابق مجموع البنود (" . number_format($calculatedTotal, 2) . ")"
                            );
                        }
                    }
                }
            }
        });
    }
}

