<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Products\DestroyProductAction;
use App\Actions\Mobile\Products\FetchProductsAction;
use App\Actions\Mobile\Products\GetProductAction;
use App\Actions\Mobile\Products\ImportProductAction;
use App\Actions\Mobile\Products\LessExportProductAction;
use App\Actions\Mobile\Products\ListProductsAction;
use App\Actions\Mobile\Products\SaveProductAction;
use App\Actions\Mobile\Products\UpdateProductAction;
use App\Dtos\Mobile\Products\FetchProductRequest;
use App\Dtos\Mobile\Products\Import\ImportProductRequest;
use App\Dtos\Mobile\Products\SaveProductRequest;
use App\Dtos\Mobile\Products\UpdateProductRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Actions\Mobile\Products\DestroyAllProductAction;
use App\Dtos\Mobile\Products\DestroyAllRequest;

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