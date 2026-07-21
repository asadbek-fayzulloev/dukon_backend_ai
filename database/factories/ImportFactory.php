<?php

namespace Database\Factories;

use App\Models\Import;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Import>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class ImportFactory extends Factory
{
    protected $model = Import::class;

    public function definition(): array
    {
        return [
            'completed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),  // nullable
            'file_name' => $this->faker->name(),
            'file_path' => $this->faker->filePath(),
            'importer' => ucfirst($this->faker->words(3, true)),
            'processed_rows' => $this->faker->numberBetween(1, 1000),
            'total_rows' => $this->faker->numberBetween(1000, 500000),
            'successful_rows' => $this->faker->numberBetween(1, 1000),
            'user_id' => User::factory(),
        ];
    }
}
