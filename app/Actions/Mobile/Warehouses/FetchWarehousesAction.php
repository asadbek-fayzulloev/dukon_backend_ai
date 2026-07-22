<?php

namespace App\Actions\Mobile\Warehouses;

use App\Dtos\Mobile\Warehouses\FetchWarehousesDTO;
use App\Models\Warehouse;

class FetchWarehousesAction
{
    public function handle():array
    {
        $warehouses = Warehouse::query()->get();

        return [
            'warehouses' => FetchWarehousesDTO::collect($warehouses)->toArray()
        ];
    }
}