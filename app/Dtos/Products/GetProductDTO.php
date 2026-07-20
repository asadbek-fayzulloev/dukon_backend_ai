<?php

namespace App\Dtos\Products;

use App\Models\ProductCategory;
use App\Models\Unit;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class GetProductDTO extends Data
{
    public int $id;
    public string $name;
    public float $price;
    public float $net_price;
    public float $quantity;
    public ?float $notify_limit;

    #[LoadRelation]
    public Unit $unit;
    #[LoadRelation]
    public ?ProductCategory $category;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'net_price' => $this->net_price,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'notify_limit' => $this->notify_limit,
            'unit_name' => $this->unit?->name,
            'unit_id' => $this->unit?->id,
            'category_id' => $this->category?->id,
            'category_name' => $this->category?->name
        ];

    }
}