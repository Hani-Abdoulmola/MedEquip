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
            // 👤 المشتري (يُعيّن تلقائياً من المستخدم المصادق عليه)
            'buyer_id' => [
                'nullable',
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

            // 📦 RFQ Items - must have at least one item
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.item_name' => ['required', 'string', 'max:200'],
            'items.*.specifications' => ['nullable', 'string', 'max:5000'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
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
            'items.required' => 'يجب إضافة على الأقل بند واحد إلى الطلب.',
            'items.min' => 'يجب إضافة على الأقل بند واحد إلى الطلب.',
            'items.*.item_name.required' => 'اسم البند مطلوب.',
            'items.*.quantity.required' => 'الكمية مطلوبة.',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر.',
        ];
    }

    /**
     * 🧠 تحقق إضافي بعد الفالديشن
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
            $rfq = $this->route('rfq');

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

            // 📅 Validate deadline on update - prevent setting to past date
            if ($isUpdate && $rfq && $this->has('deadline') && $this->deadline !== null) {
                $deadline = \Carbon\Carbon::parse($this->deadline);
                if ($deadline->isPast() && !$deadline->isToday()) {
                    $validator->errors()->add('deadline', 'لا يمكن تعيين الموعد النهائي لتاريخ في الماضي.');
                }
            }
        });
    }
}
