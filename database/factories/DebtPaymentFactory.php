<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtPayment>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class DebtPaymentFactory extends Factory
{
    protected $model = DebtPayment::class;

    public function definition(): array
    {
        return [
            'debt_id' => Debt::factory(),
            'payment_type' => $this->faker->randomElement(['default', 'other']),
            'amount' => $this->faker->numberBetween(1000, 500000),
            'paid_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
