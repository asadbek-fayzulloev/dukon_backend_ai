<?php

namespace App\Dtos\Admin\Products;


use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class UpdateProductRequest extends Data
{
    public string $name;
    public ?float $notify_limit;
    #[Exists('units', 'id')]
    public int $unit_id;
    #[Exists('product_categories', 'id')]
    public int $category_id;
}
