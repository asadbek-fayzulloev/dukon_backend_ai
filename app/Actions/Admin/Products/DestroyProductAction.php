<?php

namespace App\Actions\Admin\Products;

use App\Models\Product;

class DestroyProductAction
{
    public function handle(int $id): string
    {
        $product = Product::query()->where('company_id', user()->company_id)->find($id);
        error_if($product === null, __('products.not_found'));
        $product->delete();
        return __('products.deleted');
    }
}
