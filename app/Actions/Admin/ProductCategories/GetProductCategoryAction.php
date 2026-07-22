<?php

namespace App\Actions\Admin\ProductCategories;

use App\Dtos\Admin\ProductCategories\FetProductCategoryDTO;
use App\Models\ProductCategory;

class GetProductCategoryAction
{
    public function handle(int $id): array
    {
        $productCategory = ProductCategory::query()->find($id);
        error_if($productCategory === null, __('product_categories.not_found'));
        return [
            'product_category' => FetProductCategoryDTO::from($productCategory)->toArray()
        ];
    }
}