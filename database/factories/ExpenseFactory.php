<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => rand(1, User::query()->count()),
            'description'     => fake()->sentence,
            'date'            => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'amount_in_cents' => fake()->numberBetween(1000, 100000),
        ];
    }
}
