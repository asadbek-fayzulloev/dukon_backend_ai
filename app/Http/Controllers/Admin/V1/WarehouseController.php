<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Warehouses\FetchWarehousesAction;
use App\Actions\Admin\Warehouses\Products\AddWarehouseProductAction;
use App\Actions\Admin\Warehouses\SaveWarehousesAction;
use App\Dtos\Admin\Warehouses\SaveWarehouseRequest;
use App\Http\Controllers\ApiBaseController;
use App\Dtos\Admin\Warehouses\UpdateWarehouseRequest;
use App\Actions\Admin\Warehouses\UpdateWarehousesAction;
use App\Actions\Admin\Warehouses\Products\FetchWarehouseProductsAction;
use App\Dtos\Admin\Warehouses\Products\AddWarehouseProductRequest;
use App\Dtos\Admin\Warehouses\Products\FetchWarehouseProductsRequest;
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