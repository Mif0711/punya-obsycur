<?php

namespace App\Http\Requests;

use App\Models\BookRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VoteBookRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null || ! $user->isMember()) {
            return false;
        }

        $bookRequest = $this->route('bookRequest');

        return ! ($bookRequest instanceof BookRequest && $bookRequest->user_id === $user->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookRequest = $this->route('bookRequest');

        $alreadyVoted = $bookRequest instanceof BookRequest
            && $bookRequest->votes()->where('user_id', $this->user()?->id)->exists();

        return [
            'comment' => $alreadyVoted
                ? ['sometimes']
                : ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'comment.required' => 'Alasan vote wajib diisi.',
            'comment.min' => 'Alasan vote minimal 3 karakter.',
        ];
    }
}
