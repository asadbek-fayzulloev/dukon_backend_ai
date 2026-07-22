<?php

namespace App\Actions\Admin\Warehouses;

use App\Dtos\Admin\Warehouses\FetchWarehousesDTO;
use App\Models\Warehouse;
use App\Dtos\Admin\Warehouses\SaveWarehouseRequest;
class SaveWarehousesAction
{
    public function handle(SaveWarehouseRequest $request):string
    {
        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->shop_id = $request->shop_id;
        $warehouse->company_id = user()->company_id;
        $warehouse->save();
        return __('warehouses.saved');
    }
}