<?php

namespace App\Http\Requests\Consultant;

use Illuminate\Foundation\Http\FormRequest;

class OpenSiteVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'site_id.required' => 'يرجى اختيار الموقع الميداني من القائمة المنسدلة.',
            'site_id.exists' => 'الموقع الميداني المختار غير موجود بالسجلات.',
        ];
    }
}
