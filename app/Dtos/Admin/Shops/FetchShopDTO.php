<?php

namespace App\Dtos\Admin\Shops;

use Spatie\LaravelData\Data;

class FetchShopDTO extends Data
{
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
