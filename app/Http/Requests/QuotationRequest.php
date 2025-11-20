<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحكم في الوصول يتم عبر Middleware و Roles
    }

    public function rules(): array
    {
        $id = $this->route('quotation')?->id;

        return [
            // 🔗 العلاقة بالطلب (RFQ)
            'rfq_id' => [
                'required',
                'exists:rfqs,id',
            ],

            // 🏢 المورد
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                // 🔒 لا يمكن للمورد تقديم أكثر من عرض لنفس RFQ
                Rule::unique('quotations')
                    ->where(fn ($query) => $query->where('rfq_id', $this->rfq_id))
                    ->ignore($id),
            ],

            // 💰 السعر الإجمالي
            'total_price' => [
                'required',
                'numeric',
                'min:1',
                'max:9999999.99',
            ],

            // 📄 الشروط التجارية
            'terms' => 'nullable|string|max:2000',

            // 🏷️ الحالة
            'status' => [
                'required',
                Rule::in(['pending', 'reviewed', 'accepted', 'rejected', 'cancelled']),
            ],

            // 📅 صلاحية العرض
            'valid_until' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'rfq_id.required' => 'طلب عرض السعر مطلوب.',
            'rfq_id.exists' => 'الطلب المحدد غير موجود.',
            'supplier_id.required' => 'المورد مطلوب.',
            'supplier_id.exists' => 'المورد المحدد غير موجود.',
            'supplier_id.unique' => 'هذا المورد قدم عرضًا بالفعل لنفس الطلب.',
            'total_price.required' => 'إجمالي السعر مطلوب.',
            'total_price.numeric' => 'السعر يجب أن يكون رقمًا.',
            'total_price.min' => 'السعر يجب أن يكون أكبر من صفر.',
            'total_price.max' => 'القيمة المدخلة مرتفعة جدًا.',
            'status.required' => 'حالة العرض مطلوبة.',
            'status.in' => 'قيمة الحالة غير صحيحة.',
            'valid_until.after_or_equal' => 'تاريخ الصلاحية يجب أن يكون اليوم أو بعده.',
        ];
    }

    /**
     * ⚙️ عمليات تحقق إضافية بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 🧠 تأكد أن المورد المرتبط فعلاً هو المستخدم الحالي
            if (auth()->user()->hasRole('Supplier') && auth()->user()->supplierProfile) {
                if ($this->supplier_id != auth()->user()->supplierProfile->id) {
                    $validator->errors()->add('supplier_id', 'لا يمكنك تقديم عرض نيابة عن مورد آخر.');
                }
            }

            // ⏰ تحقق من أن RFQ مازال مفتوح
            if ($this->rfq_id) {
                $rfq = \App\Models\Rfq::find($this->rfq_id);
                if ($rfq && $rfq->status !== 'open') {
                    $validator->errors()->add('rfq_id', 'هذا الطلب لم يعد مفتوحًا لتقديم العروض.');
                }
            }
        });
    }
}
