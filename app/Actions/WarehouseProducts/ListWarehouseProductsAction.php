<?php

namespace App\Actions\WarehouseProducts;

use App\Dtos\WarehouseProducts\ListWarehouseProductsDTO;
use App\Dtos\WarehouseProducts\ListWarehouseProductsRequest;
use App\Models\WarehouseProduct;
use App\Filters\WarehouseProductFilter;
class ListWarehouseProductsAction
{
    public function handle(ListWarehouseProductsRequest $request): array
    {
        $query = WarehouseProduct::query()
            ->with(['product.unit', 'product']);

        $query = (new WarehouseProductFilter($query))->apply();
        $products = $query->get();
        
        return [
            'products' => ListWarehouseProductsDTO::collect($products)->toArray(),
        ];
    }
}