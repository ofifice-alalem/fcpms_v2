<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => 'required|array',
            'settings.*.setting_key' => 'required|string',
            'settings.*.setting_value' => 'nullable',
            'settings.*.group' => 'nullable|string',
            'settings.*.description' => 'nullable|string',
        ];
    }
}
