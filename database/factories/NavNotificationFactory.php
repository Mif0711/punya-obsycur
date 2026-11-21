<?php

namespace Database\Factories;

use App\Models\NavNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NavNotification>
 */
class NavNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nav_key' => 'member.books',
            'last_seen_at' => now()->subDay(),
        ];
    }
}
