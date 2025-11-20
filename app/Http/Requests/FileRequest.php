<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,zip',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'type' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'الملف مطلوب.',
            'file.mimes' => 'نوع الملف غير مدعوم.',
            'file.max' => 'الملف يجب أن لا يتجاوز 10 ميجابايت.',
        ];
    }
}
