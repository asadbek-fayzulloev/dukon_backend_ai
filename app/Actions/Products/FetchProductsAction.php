<?php

namespace App\Actions\Products;

use App\Dtos\PaginationDTO;
use App\Dtos\Products\FetchProductDTO;
use App\Dtos\Products\FetchProductRequest;
use App\Filters\ProductFilter;
use App\Models\Product;

class FetchProductsAction
{
    public function handle(FetchProductRequest $request): array
    {
        $query = Product::query()->orderByDesc('created_at');
        $query = (new ProductFilter($query))->apply();

        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $products = array_map(
            fn($product) => FetchProductDTO::from($product)->toArray(),
            $paginator->items()
        );

        return [
            'products' => $products,
            'paginator' => new PaginationDTO($paginator)
        ];
    }
}