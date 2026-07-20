<?php

namespace App\Actions\Warehouses;

use App\Dtos\Warehouses\FetchWarehousesDTO;
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