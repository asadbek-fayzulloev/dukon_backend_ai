<?php

namespace App\Actions\Admin\Warehouses;

use App\Models\Warehouse;
use App\Dtos\Admin\Warehouses\UpdateWarehouseRequest;
class UpdateWarehousesAction
{
    public function handle(int $id, UpdateWarehouseRequest $request):string
    {
        $warehouse = Warehouse::query()->where('company_id', user()->company_id)->find($id);
        error_if($warehouse === null, __('warehouses.not_found'));
        $warehouse->name = $request->name ?? $warehouse->name;
        $warehouse->shop_id = $request->shop_id ?? $warehouse->shop_id;
        $warehouse->save();
        return __('warehouses.saved');
    }
}