<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),  // nullable
            'seller_id' => Seller::factory(),
            'shop_id' => Shop::factory(),
            'order_total_price' => $this->faker->numberBetween(1000, 500000),
            'discount' => $this->faker->randomFloat(2, 1, 1000),  // nullable
            'order_total_paid' => $this->faker->numberBetween(1000, 500000),
            'uuid' => $this->faker->uuid(),  // nullable
            'warehouse_id' => Warehouse::factory(),  // nullable
            'device_id' => Device::factory(),  // nullable
            'subtotal' => $this->faker->numberBetween(1000, 500000),
            'discount_type' => $this->faker->randomElement(['default', 'other']),  // nullable
            'discount_value' => $this->faker->randomFloat(2, 1, 1000),  // nullable
            'discount_amount' => $this->faker->numberBetween(1000, 500000),
            'debt_amount' => $this->faker->numberBetween(1000, 500000),
            'status' => $this->faker->randomElement(['pending', 'active', 'completed']),
            'sold_at' => $this->faker->dateTimeBetween('-6 months', 'now'),  // nullable
            'synced_at' => $this->faker->dateTimeBetween('-6 months', 'now'),  // nullable
        ];
    }
}
