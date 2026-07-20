<?php

namespace App\Actions\Warehouses;

use App\Dtos\Warehouses\FetchWarehousesDTO;
use App\Models\Warehouse;
use App\Dtos\Warehouses\SaveWarehouseRequest;
class SaveWarehousesAction
{
    public function handle(SaveWarehouseRequest $request):string
    {
        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->shop_id = $request->shop_id;
        $warehouse->save();
        return __('warehouses.saved');
    }
}