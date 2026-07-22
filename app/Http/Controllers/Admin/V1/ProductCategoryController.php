<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\ProductCategories\DestroyProductCategoryAction;
use App\Actions\Admin\ProductCategories\FetchProductCategoriesAction;
use App\Actions\Admin\ProductCategories\GetProductCategoryAction;
use App\Actions\Admin\ProductCategories\SaveProductCategoryAction;
use App\Actions\Admin\ProductCategories\UpdateProductCategoryAction;
use App\Dtos\Admin\ProductCategories\SaveProductCategoryRequest;
use App\Dtos\Admin\ProductCategories\UpdateProductCategoryRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiBaseController
{
    public function index(Request $request, FetchProductCategoriesAction $action): array
    {
        return $action->handle($request);
    }

    public function show(int $id, GetProductCategoryAction $action): array
    {
        return $action->handle($id);
    }

    public function store(Request $request, SaveProductCategoryAction $action): string
    {
        return $action->handle(SaveProductCategoryRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateProductCategoryAction $action): string
    {
        return $action->handle($id, UpdateProductCategoryRequest::from($request));
    }

    public function destroy(int $id, Request $request, DestroyProductCategoryAction $action): string
    {
        return $action->handle($id);
    }
}