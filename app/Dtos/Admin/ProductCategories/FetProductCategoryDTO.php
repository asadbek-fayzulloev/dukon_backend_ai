<?php

namespace App\Dtos\Admin\ProductCategories;

use Spatie\LaravelData\Data;

class FetProductCategoryDTO extends Data
{
    public string $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}