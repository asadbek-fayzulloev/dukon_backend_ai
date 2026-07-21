<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WarehouseProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_price' => $this->faker->numberBetween(1000, 500000),
            'quantity' => $this->faker->randomFloat(3, 1, 100),
            'discount' => $this->faker->numberBetween(1, 1000),  // nullable
            'total_price' => $this->faker->numberBetween(1000, 500000),
            'warehouse_product_id' => WarehouseProduct::factory(),  // nullable
            'net_price' => $this->faker->numberBetween(1000, 500000),
        ];
    }
}
