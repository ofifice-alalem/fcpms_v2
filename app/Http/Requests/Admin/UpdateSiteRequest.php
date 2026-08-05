<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siteId = $this->route('site') ? $this->route('site')->id ?? $this->route('site') : null;

        return [
            'code'    => ['required', 'string', 'max:50', 'unique:sites,code,' . $siteId],
            'name'    => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city'    => ['nullable', 'string', 'max:100'],
            'status'  => ['nullable', 'string', 'in:active,inactive'],
            'notes'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'رمز الموقع مطلوب.',
            'code.unique'   => 'رمز الموقع مستخدم بالفعل.',
            'name.required' => 'اسم الموقع مطلوب.',
        ];
    }
}
