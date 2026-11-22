<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Normalize the username before unique validation runs.
     */
    protected function prepareForValidation(): void
    {
        $username = $this->input('username');

        if (is_string($username)) {
            $this->merge(['username' => Str::lower(trim($username))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_.-]+$/i',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'school' => ['nullable', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'phone' => ['nullable', 'string', 'max:30'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'name.required' => 'Nama wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ];
    }
}
