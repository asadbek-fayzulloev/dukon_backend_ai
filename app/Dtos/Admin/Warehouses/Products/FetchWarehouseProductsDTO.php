<?php

namespace App\Dtos\Admin\Warehouses\Products;

use App\Models\Product;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class FetchWarehouseProductsDTO extends Data
{
    public int $product_id;
    public int $warehouse_id;
    public float $quantity;
    public int $price;

    #[LoadRelation]
    public ?Product $product;

    public function toArray(): array
    {
        return [
            'id' => $this->product_id,
            'product_id' => $this->product_id,
            'name' => $this->product?->name,
            'barcode' => $this->product?->code,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'unit_name' => $this->product?->unit?->name,
        ];
    }
}
