<?php

namespace App\Dtos\WarehouseProducts;

use App\Models\Product;
use App\Models\Unit;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class ListWarehouseProductsDTO extends Data
{
    public int $id;
    #[LoadRelation]
    public ?Product $product;
    public int $product_id;
    public int $warehouse_id;
    public int $quantity;
    public float $net_price;
    public float $price;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->product?->name,
            'warehouse_id' => $this->warehouse_id,
            'net_price' => $this->net_price,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'unit_name' => $this->product->unit?->name,
        ];
    }
}