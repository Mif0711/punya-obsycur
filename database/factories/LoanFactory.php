<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
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
            'book_id' => Book::factory(),
            'loan_date' => now()->subDays(5),
            'due_date' => now()->addDays(2),
            'status' => Loan::STATUS_BORROWED,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => Loan::STATUS_PENDING]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Loan::STATUS_RETURNED,
            'return_date' => now()->subDay(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Loan::STATUS_BORROWED,
            'due_date' => now()->subDays(4),
        ]);
    }
}
