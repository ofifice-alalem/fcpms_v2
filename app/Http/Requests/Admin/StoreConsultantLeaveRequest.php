<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultantLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultant_id' => ['required', 'exists:consultants,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'consultant_id.required' => 'يرجى اختيار الاستشاري.',
            'consultant_id.exists' => 'الاستشاري المختار غير موجود في النظام.',
            'start_date.required' => 'تاريخ بدء الإجازة مطلوب.',
            'end_date.required' => 'تاريخ نهاية الإجازة مطلوب.',
            'end_date.after_or_equal' => 'تاريخ نهاية الإجازة يجب أن يكون مساوياً أو بعد تاريخ البدء.',
        ];
    }
}
