<?php

namespace App\Dtos\WarehouseProducts;

use Spatie\LaravelData\Data;

class ListWarehouseProductsRequest extends Data
{
    public ?int $warehouseId;
    public ?string $code;
    public ?string $name;
    public ?int $category_id;
}