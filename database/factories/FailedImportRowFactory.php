<?php

namespace Database\Factories;

use App\Models\FailedImportRow;
use App\Models\Import;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FailedImportRow>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class FailedImportRowFactory extends Factory
{
    protected $model = FailedImportRow::class;

    public function definition(): array
    {
        return [
            'data' => [],
            'import_id' => Import::factory(),
            'validation_error' => $this->faker->paragraph(),  // nullable
        ];
    }
}
