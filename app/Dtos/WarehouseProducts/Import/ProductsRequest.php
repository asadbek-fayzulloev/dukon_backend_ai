<?php

namespace App\Dtos\WarehouseProducts\Import;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class ProductsRequest extends Data
{
    #[Exists('products', 'id')]
    public int $id;
    public float $quantity;
    public float $price;
    public float $net_price;

}