<?php

namespace App\Actions\Admin\Products;

use App\Dtos\Admin\Products\ListProductDTO;
use App\Filters\ProductFilter;
use App\Models\Product;
use Illuminate\Http\Request;

class ListProductsAction
{
    public function handle(Request $request): array
    {
        $query = Product::query()
            ->with(['unit'])
            ->orderByDesc('created_at')
            ->limit(10);
        $query = (new ProductFilter($query))->apply();
        $products = $query->get();
        return [
            'products' => ListProductDTO::collect($products)->toArray()
        ];
    }
}