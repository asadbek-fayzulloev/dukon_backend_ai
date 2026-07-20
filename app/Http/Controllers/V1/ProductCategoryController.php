<?php

namespace App\Http\Controllers\V1;

use App\Actions\ProductCategories\DestroyProductCategoryAction;
use App\Actions\ProductCategories\FetchProductCategoriesAction;
use App\Actions\ProductCategories\GetProductCategoryAction;
use App\Actions\ProductCategories\SaveProductCategoryAction;
use App\Actions\ProductCategories\UpdateProductCategoryAction;
use App\Dtos\ProductCategories\SaveProductCategoryRequest;
use App\Dtos\ProductCategories\UpdateProductCategoryRequest;
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