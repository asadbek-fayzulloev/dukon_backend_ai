<?php

namespace App\Actions\Products;

use App\Dtos\Products\UpdateProductRequest;
use App\Models\Product;

class UpdateProductAction
{
    public function handle(int $id, UpdateProductRequest $request): string
    {
        $product = Product::query()->find($id);
        error_if($product === null, __('products.not-found'));
        $product->name = $request->name ?? $product->name;
        $product->net_price = $request->net_price ?? $product->net_price;
        $product->price = $request->price ?? $product->price;
        $product->quantity = $request->quantity ?? $product->quantity;
        $product->notify_limit = $request->notify_limit ?? $product->notify_limit;
        $product->unit_id = $request->unit_id ?? $product->unit_id;
        $product->category_id = $request->category_id ?? $product->category_id;
        $product->save();
        return __('products.updated');
    }
}