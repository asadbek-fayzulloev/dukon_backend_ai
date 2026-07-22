<?php

namespace App\Dtos\Mobile\ProductCategories;

use Spatie\LaravelData\Data;

class FetchProductCategoriesDTO extends Data
{
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];

    }
}