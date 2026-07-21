<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function PHPSTORM_META\map;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::query()->insert([
        [
            'name' => 'Potensiya',
            'unit_id' => 2,
            'category_id'=> 1
        ],
        [
            'name' => 'Sprayfert 239',
            'unit_id'=> 1,
            'category_id'=> 2

        ],
        [
            'name' => 'Vigroot',
            'unit_id' => 2,
            'category_id'=> 1

        ]
    ]);
    }
}
