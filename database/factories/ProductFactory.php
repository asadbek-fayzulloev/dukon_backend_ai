<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'image' => $this->faker->imageUrl(),  // nullable
            'unit_id' => Unit::factory(),
            'category_id' => Category::factory(),
            'notify_limit' => $this->faker->randomFloat(2, 1, 1000),  // nullable
            'uzd_price' => $this->faker->randomFloat(2, 1000, 500000),  // nullable
            'code' => $this->faker->unique()->ean13(),  // nullable
        ];
    }
}
