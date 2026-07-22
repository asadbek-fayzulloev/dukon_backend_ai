<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\WarehouseProducts\ListWarehouseProductsAction;
use App\Dtos\Mobile\WarehouseProducts\ListWarehouseProductsRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use App\Actions\Mobile\WarehouseProducts\ImportProductAction;
use App\Dtos\Mobile\WarehouseProducts\Import\ImportProductRequest;
use App\Actions\Mobile\WarehouseProducts\FetchProductMovementsAction;
use App\Dtos\Mobile\WarehouseProducts\FetchProductMovementsRequest;

class WarehouseProductController extends ApiBaseController
{
    public function index(Request $request, ListWarehouseProductsAction $action):array
    {
        return $action->handle(ListWarehouseProductsRequest::from($request));
    }
    public function import(Request $request, ImportProductAction $action): string
    {
        return $action->handle(ImportProductRequest::from($request));
    }
    public function movements(Request $request, FetchProductMovementsAction $action): array
    {
        return $action->handle(FetchProductMovementsRequest::from($request));
    }
}
