<?php

namespace App\Actions\Admin\Products;

use App\Dtos\Admin\Products\SaveProductRequest;
use App\Models\Product;

class SaveProductAction
{
    public function handle(SaveProductRequest $request): string
    {
        $product = new Product();
        $product->name = $request->name;
        $product->net_price = $request->net_price;
        $product->price = $request->price;
        $product->unit_id = $request->unit_id;
        $product->quantity = $request->quantity;
        $product->notify_limit = $request->notify_limit;
        $product->category_id = $request->category_id;
        $product->save();
        return __('products.saved');
    }
}