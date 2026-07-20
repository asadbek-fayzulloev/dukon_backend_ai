<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WarehouseProduct;
class WarehouseProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WarehouseProduct::query()->create(
            [
                'product_id' => Product::query()->inRandomOrder()->first()->id,
                'warehouse_id' => Warehouse::query()->first()->id,
                'quantity' => 100,
                'net_price' => 10.00,
                'price' => 12.00,
            ]
        );
    }
}
