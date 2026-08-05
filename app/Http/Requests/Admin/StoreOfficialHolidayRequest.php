<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficialHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required_without:holiday_date', 'nullable', 'date'],
            'end_date' => ['required_without:holiday_date', 'nullable', 'date', 'after_or_equal:start_date'],
            'holiday_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المناسبة أو العطلة الرسمية مطلوب.',
            'start_date.required_without' => 'تاريخ بدء العطلة الرسمية مطلوب.',
            'end_date.after_or_equal' => 'تاريخ نهاية العطلة يجب أن يكون مساوياً أو بعد تاريخ البدء.',
        ];
    }
}
