<?php

namespace App\Dtos\Admin\Products;

use App\Models\ProductCategory;
use App\Models\Unit;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\LoadRelation;
class ListProductDTO extends Data
{
    public int $id;
    public string $name;
    #[LoadRelation]
    public ?Unit $unit;
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit_name' => $this->unit?->name ?? '',
        ];

    }
}