<?php

namespace App\Http\Requests;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isMember() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Book $book */
                $book = $this->route('book');
                $hasRead = Loan::query()
                    ->whereBelongsTo($this->user())
                    ->where('book_id', $book->id)
                    ->where('status', Loan::STATUS_RETURNED)
                    ->exists();

                if (! $hasRead) {
                    $validator->errors()->add('rating', 'Anda hanya bisa mengulas buku yang sudah dipinjam dan dikembalikan.');
                }
            },
        ];
    }
}
