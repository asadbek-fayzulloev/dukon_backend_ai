<?php

namespace App\Dtos\Admin\WarehouseProducts;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class ListWarehouseProductsRequest extends Data
{
    #[Exists('warehouses', 'id')]
    public ?int $warehouse_id;
    public ?string $code;
    public ?string $name;
    public ?int $category_id;
}
