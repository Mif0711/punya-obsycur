<?php

namespace Database\Factories;

use App\Models\MemberCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberCard>
 */
class MemberCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'card_number' => 'PPS-'.now()->format('Y').'-'.strtoupper(bin2hex(random_bytes(3))),
            'issued_at' => now()->subDays(fake()->numberBetween(1, 365)),
            'status' => 'active',
        ];
    }
}
