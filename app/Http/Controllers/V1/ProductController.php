<?php

namespace App\Http\Controllers\V1;

use App\Actions\Products\DestroyProductAction;
use App\Actions\Products\FetchProductsAction;
use App\Actions\Products\GetProductAction;
use App\Actions\Products\ImportProductAction;
use App\Actions\Products\LessExportProductAction;
use App\Actions\Products\ListProductsAction;
use App\Actions\Products\SaveProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Dtos\Products\FetchProductRequest;
use App\Dtos\Products\Import\ImportProductRequest;
use App\Dtos\Products\SaveProductRequest;
use App\Dtos\Products\UpdateProductRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Actions\Products\DestroyAllProductAction;
use App\Dtos\Products\DestroyAllRequest;

class ProductController extends ApiBaseController
{
    public function list(Request $request, ListProductsAction $action): array
    {
        return $action->handle($request);
    }

    public function index(Request $request, FetchProductsAction $action): array
    {
        return $action->handle(FetchProductRequest::from($request));
    }

    public function show(int $id, GetProductAction $action): array
    {
        return $action->handle($id);
    }

    public function store(Request $request, SaveProductAction $action): string
    {
        return $action->handle(SaveProductRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateProductAction $action): string
    {
        return $action->handle($id, UpdateProductRequest::from($request));
    }

    public function import(Request $request, ImportProductAction $action): string
    {
        return $action->handle(ImportProductRequest::from($request));
    }

    public function exportLowStock(LessExportProductAction $action): BinaryFileResponse
    {
        return $action->handle();
    }

    public function destroy(int $id, DestroyProductAction $action): string
    {
        return $action->handle($id);
    }
    public function destroyAll(Request $request, DestroyAllProductAction $action): string
    {
        return $action->handle(DestroyAllRequest::from($request));
    }
}