<?php

namespace App\Http\Controllers\V1;

use App\Actions\WarehouseProducts\ListWarehouseProductsAction;
use App\Dtos\WarehouseProducts\ListWarehouseProductsRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use App\Actions\WarehouseProducts\ImportProductAction;
use App\Dtos\WarehouseProducts\Import\ImportProductRequest;

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
}