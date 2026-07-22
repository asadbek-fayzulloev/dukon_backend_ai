<?php

namespace App\Dtos\Mobile\Products;

use App\Models\Unit;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class FetchProductDTO extends Data
{
    public int $id;
    public string $name;
    public float $price;
    public float $net_price;
    public float $quantity;
    public ?float $notify_limit;
    #[LoadRelation]
    public ?Unit $unit;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit_name' => $this->unit?->name,
            'notify_limit' => $this->notify_limit,
        ];

    }
}