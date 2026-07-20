<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Unit;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShopSeeder::class,
            WarehouseSeeder::class,
            AdminSeeder::class,
            UnitSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            WarehouseProductSeeder::class,
        ]);
    }
}
