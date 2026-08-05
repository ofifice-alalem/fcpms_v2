<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'task_type' => ['required', Rule::enum(TaskType::class)],
            'is_active' => ['boolean'],
            'display_order' => ['nullable', 'integer'],
            'components' => ['required', 'array', 'min:1'],
            'components.*.temp_id' => ['nullable'],
            'components.*.label' => ['required', 'string', 'max:255'],
            'components.*.component_type' => ['required', 'string'],
            'components.*.placeholder' => ['nullable', 'string'],
            'components.*.is_required' => ['nullable', 'boolean'],
            'components.*.display_order' => ['nullable', 'integer'],
            'components.*.conditional_parent_id' => ['nullable'],
            'components.*.conditional_parent_temp_id' => ['nullable'],
            'components.*.conditional_value' => ['nullable', 'string'],
            'components.*.options' => ['nullable', 'array'],
            'site_ids' => ['nullable', 'array'],
            'site_ids.*' => ['exists:sites,id'],
            'consultant_ids' => ['nullable', 'array'],
            'consultant_ids.*' => ['exists:consultants,id'],
        ];
    }
}
