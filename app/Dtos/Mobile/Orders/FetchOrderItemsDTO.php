<?php

namespace App\Dtos\Mobile\Orders;

use App\Models\Product;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class FetchOrderItemsDTO extends Data
{
    public int $id;
    #[LoadRelation]
    public Product $product;
    public float $quantity;
    public int $product_price;
    public ?int $discount;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->product->name,
            'quantity' => $this->quantity,
            'product_price' => $this->product_price,
        ];
    }
}
