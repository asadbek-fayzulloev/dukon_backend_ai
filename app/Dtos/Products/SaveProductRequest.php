<?php

namespace App\Dtos\Products;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class SaveProductRequest extends Data
{
    public string $name;
    public float $net_price;
    public float $price;
    public float $quantity;
    public ?float $notify_limit;
    #[Exists('product_categories', 'id')]
    public ?int $category_id;
    #[Exists('units', 'id')]
    public int $unit_id;
}