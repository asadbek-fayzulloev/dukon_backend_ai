<?php

namespace App\Actions\Warehouses\Products;
use App\Dtos\Warehouses\Products\FetchWarehouseProductsDTO;
use App\Models\WarehouseProduct;
use App\Dtos\Warehouses\Products\FetchWarehouseProductsRequest;
use App\Dtos\PaginationDTO;

class FetchWarehouseProductsAction 
{
    public function handle(int $warehouseId, FetchWarehouseProductsRequest $request):array
    {
        $query = WarehouseProduct::query();
        
        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $products = array_map(
            fn($order) => FetchWarehouseProductsDTO::from($order)->toArray(),
            $paginator->items()
        );

        return [
            'products' => $products,
            'paginator' => new PaginationDTO($paginator),
        ];
    }
}