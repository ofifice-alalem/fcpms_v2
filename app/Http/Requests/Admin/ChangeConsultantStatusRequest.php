<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConsultantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeConsultantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ConsultantStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'يرجى تحديد حالة التوظيف الجديدة.',
        ];
    }
}
