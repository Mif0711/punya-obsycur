<?php

namespace App\Http\Requests;

use App\Models\MemberCard;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Normalize the card number before unique validation runs.
     */
    protected function prepareForValidation(): void
    {
        $cardNumber = $this->input('card_number');

        if (is_string($cardNumber)) {
            $this->merge(['card_number' => Str::upper(trim($cardNumber))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var MemberCard $card */
        $card = $this->route('card');

        return [
            'card_number' => [
                'required',
                'string',
                'max:32',
                'regex:/^PPS-\d{4}-[A-F0-9]{6}$/',
                Rule::unique('member_cards', 'card_number')->ignore($card->id),
            ],
            'issued_at' => ['required', 'date'],
            'status' => ['required', Rule::in([MemberCard::STATUS_ACTIVE, MemberCard::STATUS_LOST])],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $card = $this->route('card');

                if (! $card instanceof MemberCard) {
                    return;
                }

                $hasAnotherActiveCard = MemberCard::query()
                    ->where('user_id', $card->user_id)
                    ->where('status', MemberCard::STATUS_ACTIVE)
                    ->whereKeyNot($card->id)
                    ->exists();

                if ($this->input('status') === MemberCard::STATUS_ACTIVE && $hasAnotherActiveCard) {
                    $validator->errors()->add('status', 'Pengguna sudah memiliki kartu aktif lain.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'card_number.required' => 'Nomor kartu wajib diisi.',
            'card_number.regex' => 'Format nomor kartu harus PPS-TAHUN-XXXXXX.',
            'card_number.unique' => 'Nomor kartu sudah digunakan.',
            'issued_at.required' => 'Tanggal terbit wajib diisi.',
            'issued_at.date' => 'Tanggal terbit tidak valid.',
        ];
    }
}
