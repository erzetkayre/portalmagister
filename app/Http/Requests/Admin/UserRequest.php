<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'        => 'required|string|max:255',
            'email'       => "required|email|unique:main.users,email,{$id}",
            'nomor_induk' => "required|string|max:50|unique:main.users,nomor_induk,{$id}",
            'phone'       => 'nullable|string|digits_between:9,13',
            'is_active'   => 'required|boolean',
            'roles'       => 'required|array|min:1',
            'roles.*'     => 'exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'User harus memiliki minimal satu hak akses.',
            'roles.min'      => 'User harus memiliki minimal satu hak akses.',
        ];
    }
}
