<?php

namespace App\Dtos\Mobile\Warehouses\Products;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class FetchWarehouseProductsRequest extends Data
{
    public ?int $page;
    #[Min(1), Max(100)]
    public ?int $per_page;
}