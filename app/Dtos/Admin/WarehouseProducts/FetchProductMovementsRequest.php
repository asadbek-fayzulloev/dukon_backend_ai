<?php

namespace App\Dtos\Admin\WarehouseProducts;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class FetchProductMovementsRequest extends Data
{
    #[Exists('products', 'id')]
    public int $product_id;

    #[Exists('warehouses', 'id')]
    public ?int $warehouse_id;
}
