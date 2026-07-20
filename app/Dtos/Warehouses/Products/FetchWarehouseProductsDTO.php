<?php

namespace App\Dtos\Warehouses\Products;

use Spatie\LaravelData\Data;

class FetchWarehouseProductsDTO extends Data
{
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name'=> $this->name,
        ];
    }
}