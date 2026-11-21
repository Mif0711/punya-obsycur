<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fine_per_day' => ['required', 'integer', 'min:0', 'max:1000000'],
            'grace_days' => ['required', 'integer', 'min:0', 'max:30'],
            'loan_days' => ['required', 'integer', 'min:1', 'max:60'],
        ];
    }
}
