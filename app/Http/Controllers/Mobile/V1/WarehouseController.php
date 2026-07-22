<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Warehouses\FetchWarehousesAction;
use App\Actions\Mobile\Warehouses\Products\AddWarehouseProductAction;
use App\Actions\Mobile\Warehouses\SaveWarehousesAction;
use App\Dtos\Mobile\Warehouses\SaveWarehouseRequest;
use App\Http\Controllers\ApiBaseController;
use App\Dtos\Mobile\Warehouses\UpdateWarehouseRequest;
use App\Actions\Mobile\Warehouses\UpdateWarehousesAction;
use App\Actions\Mobile\Warehouses\Products\FetchWarehouseProductsAction;
use App\Dtos\Mobile\Warehouses\Products\AddWarehouseProductRequest;
use App\Dtos\Mobile\Warehouses\Products\FetchWarehouseProductsRequest;
use Illuminate\Http\Request;
class WarehouseController extends ApiBaseController
{
    public function index(FetchWarehousesAction $action): array
    {
        return $action->handle();
    }
    
    public function store(Request $request, SaveWarehousesAction $action):string{
        return $action->handle(SaveWarehouseRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateWarehousesAction $action):string
    {
        return $action->handle($id, UpdateWarehouseRequest::from($request));
    }
    public function listProducts(int $warehouseId, Request $request, FetchWarehouseProductsAction $action):array
    {
        return $action->handle($warehouseId, FetchWarehouseProductsRequest::from($request));
    }
    public function addProduct(int $warehouseId, Request $request, AddWarehouseProductAction $action):string
    {
        return $action->handle($warehouseId, AddWarehouseProductRequest::from($request));
    }
}