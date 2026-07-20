<?php

namespace App\Actions\Warehouses\Products;

use App\Dtos\Warehouses\Products\AddWarehouseProductRequest;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;

class AddWarehouseProductAction
{
    public function handle(int $warehouseId, AddWarehouseProductRequest $request):string
    {
        $warehouse = Warehouse::query()->find($warehouseId);
        error_if($warehouse === null, __('warehouses.not_found'));
        $warehouseProduct = WarehouseProduct::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $request->product_id)
            ->where('price', $request->price)
            ->where('net_price', $request->net_price)
            ->first();
        if($warehouseProduct === null){
            $warehouseProduct = new WarehouseProduct();
            $warehouseProduct->warehouse_id = $warehouse->id;
            $warehouseProduct->product_id = $request->product_id;
            $warehouseProduct->price =$request->price;
            $warehouseProduct->net_price = $request->net_price;
            $warehouseProduct->amount = $request->amount;
        } else {
            $warehouseProduct->amount = $warehouseProduct->amount + $request->amount;
        }
        
        $warehouseProduct->save();
        return __('warehouses.product_added');
    }
}