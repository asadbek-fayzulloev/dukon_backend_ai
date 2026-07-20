<?php

namespace App\Actions\ProductCategories;

use App\Dtos\ProductCategories\UpdateProductCategoryRequest;
use App\Models\ProductCategory;

class UpdateProductCategoryAction
{
    public function handle(int $id, UpdateProductCategoryRequest $request): string
    {
        $productCategory = ProductCategory::query()->find($id);
        error_if($productCategory === null, __('product_categories.not_found'));
        $productCategory->name = $request->name ?? $productCategory->name;
        $productCategory->save();
        return __('product_categories.updated');
    }
}