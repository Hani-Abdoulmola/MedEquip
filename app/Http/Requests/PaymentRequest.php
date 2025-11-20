<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات يتم التحكم بها عبر Middleware
    }

    public function rules(): array
    {
        $id = $this->route('payment')?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $rules = [
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'buyer_id' => ['nullable', 'exists:buyers,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => [
                'required',
                Rule::in([
                    Payment::CURRENCY_LYD,
                    Payment::CURRENCY_USD,
                    Payment::CURRENCY_EUR,
                ]),
            ],
            'method' => ['required', Rule::in(['cash', 'bank_transfer', 'credit_card', 'paypal', 'other'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'completed', 'failed', 'refunded'])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'paid_at' => ['nullable', 'date'],
        ];

        // ✅ أثناء التحديث يمكن أن تكون بعض الحقول اختيارية
        if ($isUpdate) {
            $rules['amount'] = ['sometimes', 'numeric', 'min:0.01'];
            $rules['method'] = ['sometimes', Rule::in(['cash', 'bank_transfer', 'credit_card', 'paypal', 'other'])];
        }

        return $rules;
    }

    /**
     * 🧠 التحقق المخصص — لا تسمح بمبلغ أكبر من الفاتورة
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->invoice_id) {
                $invoice = Invoice::with('payments')->find($this->invoice_id);
                if ($invoice) {
                    $totalPaid = $invoice->payments->sum('amount');
                    $remaining = $invoice->total_amount - $totalPaid;

                    if ($this->amount > $remaining) {
                        $validator->errors()->add(
                            'amount',
                            "المبلغ المدخل ({$this->amount}) يتجاوز المبلغ المتبقي من الفاتورة ({$remaining})."
                        );
                    }

                    // 🔗 تحقق أن المشتري والمورد في الدفع يطابقوا الفاتورة
                    if ($this->buyer_id && $this->buyer_id != $invoice->order?->buyer_id) {
                        $validator->errors()->add('buyer_id', 'المشتري المحدد لا يتوافق مع الفاتورة.');
                    }

                    if ($this->supplier_id && $this->supplier_id != $invoice->order?->supplier_id) {
                        $validator->errors()->add('supplier_id', 'المورد المحدد لا يتوافق مع الفاتورة.');
                    }
                }
            }

            // ✅ تحقق إضافي: لا يمكن تحديد فاتورة وأمر مختلفين
            if ($this->invoice_id && $this->order_id) {
                $invoice = Invoice::find($this->invoice_id);
                $order = Order::find($this->order_id);
                if ($invoice && $order && $invoice->order_id !== $order->id) {
                    $validator->errors()->add('order_id', 'الطلب لا يتطابق مع الفاتورة المحددة.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'قيمة المبلغ مطلوبة.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقمًا صحيحًا.',
            'amount.min' => 'قيمة المبلغ يجب أن تكون أكبر من صفر.',
            'currency.required' => 'العملة مطلوبة.',
            'currency.in' => 'العملة غير مدعومة. العملات المسموح بها: LYD, USD, EUR.',
            'method.required' => 'طريقة الدفع مطلوبة.',
            'method.in' => 'طريقة الدفع غير صحيحة.',
            'status.required' => 'حالة الدفع مطلوبة.',
            'status.in' => 'حالة الدفع غير صحيحة.',
            'invoice_id.exists' => 'الفاتورة غير موجودة.',
            'order_id.exists' => 'الطلب غير موجود.',
        ];
    }
}
