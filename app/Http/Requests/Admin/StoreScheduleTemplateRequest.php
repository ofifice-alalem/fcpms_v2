<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['boolean'],
            'days' => ['array'],
            'days.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'days.*.is_working_day' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم قالب الدوام مطلوب.',
            'name.max' => 'اسم القالب يجب ألا يتجاوز 255 حرفاً.',
        ];
    }
}
