<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'يرجى إدخال البريد الإلكتروني أو اسم المستخدم.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.min'      => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
        ];
    }
}
