<?php

namespace App\Dtos\Mobile\Products;

use App\Models\ProductCategory;
use App\Models\Unit;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\LoadRelation;
class ListProductDTO extends Data
{
    public int $id;
    public string $name;
    public float $price;
    public float $net_price;
    public float $quantity;
    #[LoadRelation]
    public ?Unit $unit;
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'net_price' => $this->net_price,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'unit_name' => $this->unit?->name ?? '',
        ];

    }
}