<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'date_target'   => ['nullable', 'string', 'in:work_date,updated_at'],
            'date_from'     => ['nullable', 'date'],
            'date_to'       => ['nullable', 'date', 'after_or_equal:date_from'],
            'consultant_id' => ['nullable', 'integer', 'exists:consultants,id'],
            'site_id'       => ['nullable', 'integer', 'exists:sites,id'],
            'city'          => ['nullable', 'string', 'max:100'],
        ];
    }
}
