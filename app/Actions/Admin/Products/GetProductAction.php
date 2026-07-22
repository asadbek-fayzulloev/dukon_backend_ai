<?php

namespace App\Actions\Admin\Products;

use App\Dtos\Admin\Products\GetProductDTO;
use App\Models\Product;

class GetProductAction
{
    public function handle($id): array
    {
        $product = Product::query()->find($id);
        error_if($product === null, __('products.not-found'));
        return [
            'product' => GetProductDTO::from($product)->toArray()
        ];
    }
}