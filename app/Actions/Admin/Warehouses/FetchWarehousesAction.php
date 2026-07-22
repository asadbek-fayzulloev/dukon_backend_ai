<?php

namespace App\Actions\Admin\Warehouses;

use App\Dtos\Admin\Warehouses\FetchWarehousesDTO;
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