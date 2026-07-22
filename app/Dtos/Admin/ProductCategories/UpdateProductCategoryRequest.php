<?php

namespace App\Dtos\Admin\ProductCategories;

use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UpdateProductCategoryRequest extends Data
{
    #[Unique('product_categories', 'name')]
    public ?string $name;
}