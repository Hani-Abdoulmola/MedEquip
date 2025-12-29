<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierDeliveryStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller
        return (bool) ($this->user() && $this->user()->supplierProfile);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    Delivery::STATUS_PENDING,
                    Delivery::STATUS_IN_TRANSIT,
                    Delivery::STATUS_DELIVERED,
                    Delivery::STATUS_FAILED,
                ]),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'حالة التسليم مطلوبة.',
            'status.in' => 'حالة التسليم المحددة غير صحيحة.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $delivery = $this->route('delivery');
            
            if (!$delivery) {
                return;
            }

            $oldStatus = $delivery->status;
            $newStatus = $this->input('status');

            // Prevent invalid transitions
            if ($oldStatus === Delivery::STATUS_DELIVERED && $newStatus !== Delivery::STATUS_DELIVERED) {
                $validator->errors()->add(
                    'status',
                    'لا يمكن تغيير حالة التسليم بعد التأكيد.'
                );
            }

            if ($oldStatus === Delivery::STATUS_FAILED && $newStatus === Delivery::STATUS_DELIVERED) {
                $validator->errors()->add(
                    'status',
                    'لا يمكن تأكيد التسليم بعد الفشل.'
                );
            }
        });
    }
}

