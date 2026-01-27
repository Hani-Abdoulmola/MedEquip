<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('notifications.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'string', 'in:suppliers,buyers,both,specific'],
            'recipient_ids' => ['nullable', 'array', 'required_if:recipients.*,specific'],
            'recipient_ids.*' => ['nullable', 'integer', 'exists:users,id'],
            'url' => ['nullable', 'string', 'max:500', 'url'],
            'type' => ['nullable', 'string', 'in:info,success,warning,error,primary'],
            'icon' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.max' => 'عنوان الإشعار يجب ألا يتجاوز 255 حرف',
            'message.required' => 'محتوى الإشعار مطلوب',
            'message.max' => 'محتوى الإشعار يجب ألا يتجاوز 5000 حرف',
            'recipients.required' => 'يجب اختيار المستلمين',
            'recipients.array' => 'المستلمون يجب أن يكونوا في صورة قائمة',
            'recipients.min' => 'يجب اختيار مستلم واحد على الأقل',
            'recipients.*.in' => 'نوع المستلم غير صحيح',
            'recipient_ids.required_if' => 'يجب اختيار مستلمين محددين عند اختيار "محدد"',
            'recipient_ids.array' => 'معرفات المستلمين يجب أن تكون في صورة قائمة',
            'recipient_ids.*.exists' => 'أحد المستلمين المحددين غير موجود',
            'url.url' => 'الرابط غير صحيح',
            'type.in' => 'نوع الإشعار غير صحيح',
        ];
    }
}
