<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RfqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار عبر الـ Middleware و Spatie Roles
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
        $rfqId = $this->route('rfq')?->id;

        return [
            // 👤 المشتري (يجب أن يكون موجود فعلاً)
            'buyer_id' => [
                'required',
                'exists:buyers,id',
            ],

            // 🧾 الرمز المرجعي (يُنشأ تلقائياً عند الإنشاء)
            'reference_code' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'string',
                'max:100',
                Rule::unique('rfqs', 'reference_code')->ignore($rfqId),
            ],

            // 📦 عنوان الطلب
            'title' => ['required', 'string', 'max:200'],

            // 📝 الوصف
            'description' => ['nullable', 'string', 'max:5000'],

            // 📅 الموعد النهائي لتقديم العروض
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],

            // ⚙️ الحالة
            'status' => [
                'required',
                Rule::in(['draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled']),
            ],

            // 👁️ هل الطلب عام أم خاص
            'is_public' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'buyer_id.required' => 'يجب تحديد المشتري صاحب الطلب.',
            'buyer_id.exists' => 'المشتري المحدد غير موجود في النظام.',
            'reference_code.unique' => 'الرمز المرجعي مستخدم مسبقًا.',
            'title.required' => 'العنوان مطلوب.',
            'title.max' => 'العنوان لا يمكن أن يتجاوز 200 حرف.',
            'description.max' => 'الوصف لا يمكن أن يتجاوز 5000 حرف.',
            'deadline.date' => 'صيغة التاريخ غير صحيحة.',
            'deadline.after_or_equal' => 'تاريخ الموعد يجب أن يكون اليوم أو بعده.',
            'status.required' => 'حالة الطلب مطلوبة.',
            'status.in' => 'قيمة الحالة غير صحيحة.',
            'is_public.required' => 'يجب تحديد ما إذا كان الطلب عامًا أم خاصًا.',
            'is_public.boolean' => 'القيمة يجب أن تكون نعم أو لا.',
        ];
    }

    /**
     * 🧠 تحقق إضافي بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 🧩 تأكد أن المستخدم الحالي فعلاً هو المشتري المرتبط
            if (auth()->user()->hasRole('Buyer') && auth()->user()->buyerProfile) {
                if ($this->buyer_id != auth()->user()->buyerProfile->id) {
                    $validator->errors()->add('buyer_id', 'لا يمكنك إنشاء أو تعديل RFQ نيابة عن مشتري آخر.');
                }
            }

            // 🚫 لا يمكن للمورد أو مستخدم غير مخول إنشاء RFQ
            if (auth()->user()->hasRole('Supplier')) {
                $validator->errors()->add('role', 'المورد لا يمكنه إنشاء RFQ.');
            }
        });
    }
}
