<?php

namespace App\Actions\Mobile\ProductCategories;

use App\Dtos\Mobile\ProductCategories\SaveProductCategoryRequest;
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