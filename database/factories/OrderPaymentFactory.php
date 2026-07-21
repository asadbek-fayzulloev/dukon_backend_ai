<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderPayment>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class OrderPaymentFactory extends Factory
{
    protected $model = OrderPayment::class;

    public function definition(): array
    {
        return [
            'payment_type' => $this->faker->randomElement(['default', 'other']),
            'payed_price' => $this->faker->numberBetween(1000, 500000),
            'order_id' => Order::factory(),
        ];
    }
}
