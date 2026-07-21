<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1000, 500000),
            'status' => $this->faker->randomElement(['pending', 'active', 'completed']),
            'user_id' => User::factory(),
            'is_notified' => $this->faker->boolean(),
            'order_id' => Order::factory(),
            'return_date' => $this->faker->dateTimeBetween('-6 months', 'now'),  // nullable
            'remaining_amount' => $this->faker->numberBetween(1000, 500000),  // nullable
            'paid_at' => $this->faker->dateTimeBetween('-6 months', 'now'),  // nullable
        ];
    }
}
