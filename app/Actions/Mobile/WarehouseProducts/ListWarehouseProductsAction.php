<?php

namespace App\Actions\Mobile\WarehouseProducts;

use App\Dtos\Mobile\WarehouseProducts\ListWarehouseProductsDTO;
use App\Dtos\Mobile\WarehouseProducts\ListWarehouseProductsRequest;
use App\Filters\WarehouseProductFilter;
use App\Models\WarehouseProduct;

class ListWarehouseProductsAction
{
    public function handle(ListWarehouseProductsRequest $request): array
    {
        $query = WarehouseProduct::query()
            ->where('quantity', '>', 0)
            ->whereHas('warehouse', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->when($request->warehouse_id, fn ($query, $warehouseId) => $query->where('warehouse_id', $warehouseId))
            ->with(['product.unit']);

        $query = (new WarehouseProductFilter($query))->apply();
        $products = $query
            ->selectRaw('product_id, warehouse_id, SUM(quantity) AS quantity, MAX(price) AS price')
            ->groupBy('product_id', 'warehouse_id')
            ->get();

        return ['products' => ListWarehouseProductsDTO::collect($products)->toArray()];
    }
}
