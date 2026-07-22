<?php

namespace App\Dtos\Shops;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateShopRequest extends Data
{
    #[Min(2), Max(255)]
    public string $name;
}
