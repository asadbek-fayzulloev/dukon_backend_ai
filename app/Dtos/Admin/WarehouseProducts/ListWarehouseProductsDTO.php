<?php

namespace App\Dtos\Admin\WarehouseProducts;

use App\Models\Product;
use App\Models\Unit;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class ListWarehouseProductsDTO extends Data
{
    #[LoadRelation]
    public ?Product $product;
    public int $product_id;
    public int $warehouse_id;
    public float $quantity;
    public int $price;
    public ?float $net_price;

    public function toArray(): array
    {
        return [
            'id' => $this->product_id,
            'product_id' => $this->product_id,
            'name' => $this->product?->name,
            'barcode' => $this->product?->code,
            'warehouse_id' => $this->warehouse_id,
            'price' => $this->price,
            'net_price' => $this->net_price ?? 0,
            'quantity' => $this->quantity,
            'unit_name' => $this->product?->unit?->name,
            'notify_limit' => $this->product?->notify_limit ?? 0,
        ];
    }
}
