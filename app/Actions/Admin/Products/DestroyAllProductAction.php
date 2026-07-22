<?php

namespace App\Actions\Admin\Products;

use App\Models\Product;
use App\Dtos\Admin\Products\DestroyAllRequest;

class DestroyAllProductAction
{
    public function handle(DestroyAllRequest $request): string
    {
        $product = Product::query()->where('company_id', user()->company_id)->whereIn('id', $request->ids)->delete();
        return __('products.deleted');
    }
}
