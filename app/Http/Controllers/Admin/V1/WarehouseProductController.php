<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\WarehouseProducts\ListWarehouseProductsAction;
use App\Dtos\Admin\WarehouseProducts\ListWarehouseProductsRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use App\Actions\Admin\WarehouseProducts\ImportProductAction;
use App\Dtos\Admin\WarehouseProducts\Import\ImportProductRequest;
use App\Actions\Admin\WarehouseProducts\FetchProductMovementsAction;
use App\Dtos\Admin\WarehouseProducts\FetchProductMovementsRequest;

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
