<?php

namespace App\Dtos\Shops;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveShopRequest extends Data
{
    #[Min(2), Max(255), Unique('shops', 'name')]
    public string $name;
}
