<?php

namespace App\Dtos\Admin\Orders;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class SaveOrderItemRequest extends Data
{
    #[Exists('products', 'id')]
    public int $product_id;
    #[Rule(['numeric', 'gt:0'])]
    public float $quantity;
}
