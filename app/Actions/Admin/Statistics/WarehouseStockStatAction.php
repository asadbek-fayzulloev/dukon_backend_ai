<?php

namespace App\Actions\Admin\Statistics;

use App\Models\WarehouseProduct;

class WarehouseStockStatAction
{
    public function handle(): array
    {
        $stats = WarehouseProduct::query()
            ->where('company_id', user()->company_id)
            ->with('warehouse:id,name')
            ->get()
            ->groupBy('warehouse_id')
            ->map(fn ($rows) => [
                'warehouse_name' => $rows->first()->warehouse->name ?? "Noma'lum",
                'stock_value' => (float) $rows->sum(fn ($row) => $row->price * $row->quantity),
            ])
            ->values();

        return ['warehouse_stock_stats' => $stats];
    }
}
