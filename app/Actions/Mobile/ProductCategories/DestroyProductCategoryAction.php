<?php

namespace App\Actions\Mobile\ProductCategories;

use App\Models\ProductCategory;

class DestroyProductCategoryAction
{
    public function handle(int $id): string
    {
        $productCategory = ProductCategory::query()->find($id);
        error_if($productCategory === null, __('product_categories.not_found'));
        $productCategory->delete();
        return __('product_categories.destroyed');
    }
}