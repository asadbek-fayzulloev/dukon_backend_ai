<?php

namespace App\Actions\ProductCategories;

use App\Dtos\ProductCategories\SaveProductCategoryRequest;
use App\Models\ProductCategory;

class SaveProductCategoryAction
{
    public function handle(SaveProductCategoryRequest $request): string
    {
        $productCategory = new ProductCategory();
        $productCategory->name = $request->name;
        $productCategory->save();
        return __('product_categories.stored');
    }
}