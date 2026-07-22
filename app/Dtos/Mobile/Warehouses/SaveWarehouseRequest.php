<?php

namespace App\Dtos\Mobile\Warehouses;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;

class SaveWarehouseRequest extends Data
{
    #[Min(3), Max(255), Unique('warehouses','name')]
    public string $name;
    #[Exists('shops','id')]
    public int $shop_id;
}