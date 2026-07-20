<?php

namespace App\Actions\ProductCategories;

use App\Dtos\ProductCategories\FetchProductCategoriesDTO;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class FetchProductCategoriesAction
{
    public function handle(Request $request): array
    {
        $products = ProductCategory::query()->orderByDesc('created_at')->get();
        return [
            'product_categories' => FetchProductCategoriesDTO::collect($products)->toArray()
        ];
    }
}