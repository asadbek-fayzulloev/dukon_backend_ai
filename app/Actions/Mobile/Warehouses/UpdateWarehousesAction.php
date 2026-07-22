<?php

namespace App\Actions\Mobile\Warehouses;

use App\Models\Warehouse;
use App\Dtos\Mobile\Warehouses\UpdateWarehouseRequest;
class UpdateWarehousesAction
{
    public function handle(int $id, UpdateWarehouseRequest $request):string
    {
        $warehouse = Warehouse::query()->find($id);
        error_if($warehouse === null, __('warehouses.not_found'));
        $warehouse->name = $request->name ?? $warehouse->name;
        $warehouse->shop_id = $request->shop_id ?? $warehouse->shop_id;
        $warehouse->save();
        return __('warehouses.saved');
    }
}