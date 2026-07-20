<?php

namespace App\Dtos\Orders;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class SaveOrderItemRequest extends Data
{
    #[Exists('warehouse_products', 'id')]
    public int $product_id;
    public float $product_price;
    public float $quantity;

}