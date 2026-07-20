<?php

namespace App\Dtos\ProductCategories;

use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveProductCategoryRequest extends Data
{
    #[Unique('product_categories', 'name')]
    public string $name;
}