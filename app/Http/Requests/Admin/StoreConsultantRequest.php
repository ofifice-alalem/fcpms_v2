<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConsultantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:50'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'work_schedule_template_id' => ['nullable', 'exists:work_schedule_templates,id'],
            'employment_status' => ['nullable', Rule::enum(ConsultantStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'يرجى إدخال الاسم الكامل للاستشاري.',
            'username.unique' => 'اسم المستخدم مستخدم سابقاً من قِبل حساب آخر.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صريح وسليم.',
            'email.unique' => 'البريد الإلكتروني مستخدم سابقاً من قِبل حساب آخر.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
            'work_schedule_template_id.exists' => 'قالب دوام العمل المحدد غير موجود.',
        ];
    }
}
