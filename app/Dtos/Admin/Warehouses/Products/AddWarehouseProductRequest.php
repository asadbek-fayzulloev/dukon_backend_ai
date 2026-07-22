<?php

namespace App\Dtos\Admin\Warehouses\Products;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class AddWarehouseProductRequest extends Data
{
    #[Exists('products','id')]
    public int $product_id;
    public float $price;
    public float $net_price;
    public float $amount;
}