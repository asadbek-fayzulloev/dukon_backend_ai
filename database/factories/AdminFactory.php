<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'shop_id' => Shop::factory(),  // nullable
            'email' => $this->faker->unique()->safeEmail(),  // nullable
            'password' => Hash::make('password'),  // nullable
            'remember_token' => Str::random(10),  // nullable
        ];
    }
}
