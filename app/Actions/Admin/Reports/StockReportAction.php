<?php

namespace App\Actions\Admin\Reports;

use App\Models\WarehouseProduct;

class StockReportAction
{
    public function handle(): array
    {
        $rows = WarehouseProduct::query()
            ->where('company_id', user()->company_id)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->whereHas('warehouse', fn ($q) => $q->where('shop_id', $shopId)))
            ->where('quantity', '>', 0)
            ->with(['product:id,name', 'warehouse:id,name'])
            ->get()
            ->map(fn ($row) => [
                'warehouse_name' => $row->warehouse?->name,
                'product_name' => $row->product?->name,
                'quantity' => (float) $row->quantity,
                'net_price' => (float) $row->net_price,
                'price' => (float) $row->price,
                'cost_value' => (float) $row->net_price * (float) $row->quantity,
                'sale_value' => (float) $row->price * (float) $row->quantity,
            ]);

        return [
            'rows' => $rows->values(),
            'summary' => [
                'total_cost_value' => (float) $rows->sum('cost_value'),
                'total_sale_value' => (float) $rows->sum('sale_value'),
                'potential_profit' => (float) $rows->sum('sale_value') - (float) $rows->sum('cost_value'),
            ],
        ];
    }
}
