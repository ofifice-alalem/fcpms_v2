<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConsultantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $consultant = $this->route('consultant');
        $userId = is_object($consultant) ? $consultant->user_id : null;

        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
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
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل من قبل حساب آخر.',
        ];
    }
}
