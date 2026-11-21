<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'genre_id' => ['required', 'exists:genres,id'],
            'publisher_id' => ['required', 'exists:publishers,id'],
            'author_id' => ['required', 'exists:authors,id'],
            'isbn' => ['nullable', 'string', 'max:30', Rule::unique('books', 'isbn')],
            'description' => ['nullable', 'string', 'max:5000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'editor_pick' => ['boolean'],
            'award_winner' => ['boolean'],
            'award_name' => ['nullable', 'string', 'max:150'],
            'public_figure_pick' => ['boolean'],
            'public_figure_name' => ['nullable', 'string', 'max:150'],
            'social_trend' => ['boolean'],
            'social_trend_source' => ['nullable', 'string', 'max:150'],
            'themes' => ['nullable', 'array'],
            'themes.*' => ['string', 'max:50'],
        ];
    }
}
