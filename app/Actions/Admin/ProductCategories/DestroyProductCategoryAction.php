<?php

namespace App\Actions\Admin\ProductCategories;

use App\Models\ProductCategory;

class DestroyProductCategoryAction
{
    public function handle(int $id): string
    {
        $productCategory = ProductCategory::query()->where('company_id', user()->company_id)->find($id);
        error_if($productCategory === null, __('product_categories.not_found'));
        $productCategory->delete();
        return __('product_categories.destroyed');
    }
}