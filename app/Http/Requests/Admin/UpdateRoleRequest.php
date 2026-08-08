<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
        if (is_object($roleId)) {
            $roleId = $roleId->id;
        }

        return [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم دور الصلاحيات مطلوب.',
            'name.unique' => 'اسم الدور مستخدم بالفعل في النظام.',
            'permissions.array' => 'قائمة الصلاحيات يجب أن تكون مصفوفة.',
        ];
    }
}
