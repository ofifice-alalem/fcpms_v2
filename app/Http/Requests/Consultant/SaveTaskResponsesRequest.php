<?php

namespace App\Http\Requests\Consultant;

use Illuminate\Foundation\Http\FormRequest;

class SaveTaskResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses' => ['nullable', 'array'],
            'responses.*.task_definition_id' => ['required_with:responses', 'integer', 'exists:task_definitions,id'],
            'responses.*.values' => ['nullable', 'array'],
            'responses.*.is_completed' => ['nullable', 'boolean'],
            'complete_visit' => ['nullable', 'boolean'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable'],
        ];
    }
}
