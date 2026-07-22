<?php

namespace App\Actions\Admin\Warehouses\Products;
use App\Dtos\Admin\Warehouses\Products\FetchWarehouseProductsDTO;
use App\Models\WarehouseProduct;
use App\Dtos\Admin\Warehouses\Products\FetchWarehouseProductsRequest;
use App\Dtos\PaginationDTO;

class FetchWarehouseProductsAction 
{
    public function handle(int $warehouseId, FetchWarehouseProductsRequest $request):array
    {
        $query = WarehouseProduct::query()
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->whereHas('warehouse', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->with(['product.unit'])
            ->selectRaw('product_id, warehouse_id, SUM(quantity) AS quantity, MAX(price) AS price')
            ->groupBy('product_id', 'warehouse_id');
        
        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $products = array_map(
            fn($product) => FetchWarehouseProductsDTO::from($product)->toArray(),
            $paginator->items()
        );

        return [
            'products' => $products,
            'paginator' => new PaginationDTO($paginator),
        ];
    }
}
