<?php

namespace App\Actions\Admin\ProductCategories;

use App\Dtos\Admin\ProductCategories\SaveProductCategoryRequest;
use App\Models\ProductCategory;

class SaveProductCategoryAction
{
    public function handle(SaveProductCategoryRequest $request): string
    {
        $productCategory = new ProductCategory();
        $productCategory->name = $request->name;
        $productCategory->company_id = user()->company_id;
        $productCategory->save();
        return __('product_categories.stored');
    }
}