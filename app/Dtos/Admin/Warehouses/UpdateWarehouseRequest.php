<?php

namespace App\Dtos\Admin\Warehouses;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;

class UpdateWarehouseRequest extends Data
{
    #[Min(3), Max(255)]
    public string $name;
    #[Exists('shops','id')]
    public int $shop_id;
}