<?php

namespace App\Services;

use App\Models\MemberCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MemberCardService
{
    /**
     * Issue a new membership card whenever an account is created.
     */
    public function issue(User $user): MemberCard
    {
        return MemberCard::create([
            'user_id' => $user->id,
            'card_number' => $this->generateNumber(),
            'issued_at' => today(),
            'status' => MemberCard::STATUS_ACTIVE,
        ]);
    }

    /**
     * Update card details while keeping the account status in sync with the card.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(MemberCard $card, array $data): MemberCard
    {
        $previousStatus = $card->status;

        return DB::transaction(function () use ($card, $data, $previousStatus): MemberCard {
            $card->update($data);

            if ($card->status !== $previousStatus) {
                $card->user()->update(['is_active' => $card->status === MemberCard::STATUS_ACTIVE]);
            }

            return $card->refresh();
        });
    }

    /**
     * A lost card deactivates the account until the card is replaced.
     */
    public function markLost(MemberCard $card): MemberCard
    {
        return DB::transaction(function () use ($card): MemberCard {
            $card->update([
                'status' => MemberCard::STATUS_LOST,
                'note' => 'Kartu dilaporkan hilang.',
            ]);

            $card->user()->update(['is_active' => false]);

            return $card->refresh();
        });
    }

    public function replace(MemberCard $card): MemberCard
    {
        return DB::transaction(function () use ($card): MemberCard {
            $card->update(['status' => MemberCard::STATUS_REPLACED]);

            $replacement = $this->issue($card->user);
            $card->user()->update(['is_active' => true]);

            return $replacement;
        });
    }

    private function generateNumber(): string
    {
        do {
            $number = 'PPS-'.today()->format('Y').'-'.strtoupper(bin2hex(random_bytes(3)));
        } while (MemberCard::where('card_number', $number)->exists());

        return $number;
    }
}
