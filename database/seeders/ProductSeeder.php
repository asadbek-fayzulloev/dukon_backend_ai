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
            'quantity' => 10,
            'net_price' => 400000,
            'price' => 440000,
            'unit_id' => 2,
            'category_id'=> 1
        ],
        [
            'name' => 'Sprayfert 239',
            'quantity' => 20,
            'net_price'=> 200000,
            'price' => 220000,
            'unit_id'=> 1,
            'category_id'=> 2

        ],
        [
            'name' => 'Vigroot',
            'quantity' => 500,
            'net_price' => 100000,
            'price' => 125000,
            'unit_id' => 2,
            'category_id'=> 1

        ]
    ]);
    }
}
