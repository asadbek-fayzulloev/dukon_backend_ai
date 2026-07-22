<?php

namespace App\Actions\Mobile\Products;

use App\Models\Product;
use App\Dtos\Mobile\Products\DestroyAllRequest;

class DestroyAllProductAction
{
    public function handle(DestroyAllRequest $request): string
    {
        $product = Product::query()->whereIn('id', $request->ids)->delete();
        return __('products.deleted');
    }
}
