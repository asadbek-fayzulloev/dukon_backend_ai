<?php

namespace App\Dtos\Mobile\Warehouses;

use Spatie\LaravelData\Dto;

class FetchWarehousesDTO extends Dto
{
    public int $id;
    public string $name;
    public int $shop_id;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shop_id' => $this->shop_id
        ];
    }
}