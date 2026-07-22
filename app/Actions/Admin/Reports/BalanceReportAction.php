<?php

namespace App\Actions\Admin\Reports;

use App\Dtos\Admin\Reports\BalanceReportRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Models\WarehouseProductHistory;
use Carbon\Carbon;

class BalanceReportAction
{
    /**
     * Classic turnover-balance report for one warehouse: for each product,
     * ending = beginning + kirim - chiqim, reconstructed backward from the
     * live warehouse_products quantity (the only point-in-time snapshot we
     * actually store) using buy history + sold order_items as the ledger.
     */
    public function handle(BalanceReportRequest $request): array
    {
        $companyId = user()->company_id;
        $warehouseId = $request->warehouse_id;

        $warehouse = Warehouse::query()
            ->where('company_id', $companyId)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->where('shop_id', $shopId))
            ->find($warehouseId);
        error_if($warehouse === null, __('warehouses.not_found'));

        $periodStart = Carbon::parse($request->from_date)->startOfDay();
        $periodEnd = Carbon::parse($request->to_date)->endOfDay();

        $currentByProduct = WarehouseProduct::query()
            ->where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->pluck('qty', 'product_id');

        $buyRows = WarehouseProductHistory::query()
            ->where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'buy')
            ->get(['product_id', 'quantity', 'created_at']);

        $soldRows = OrderItem::query()
            ->whereHas('order', fn ($query) => $query->where('company_id', $companyId)->where('warehouse_id', $warehouseId))
            ->with('order:id,created_at')
            ->get(['id', 'order_id', 'product_id', 'quantity']);

        $productIds = collect()
            ->merge($currentByProduct->keys())
            ->merge($buyRows->pluck('product_id'))
            ->merge($soldRows->pluck('product_id'))
            ->unique()
            ->values();

        $productNames = Product::query()->whereIn('id', $productIds)->pluck('name', 'id');

        $rows = $productIds->map(function ($productId) use (
            $currentByProduct, $buyRows, $soldRows, $periodStart, $periodEnd, $productNames
        ) {
            $currentQty = (float) ($currentByProduct[$productId] ?? 0);

            $productBuys = $buyRows->where('product_id', $productId);
            $productSales = $soldRows->where('product_id', $productId);

            $buyAfterEnd = (float) $productBuys->filter(fn ($r) => $r->created_at->gt($periodEnd))->sum('quantity');
            $soldAfterEnd = (float) $productSales
                ->filter(fn ($r) => $r->order?->created_at?->gt($periodEnd))
                ->sum('quantity');

            $buyInPeriod = (float) $productBuys
                ->filter(fn ($r) => $r->created_at->between($periodStart, $periodEnd))
                ->sum('quantity');
            $soldInPeriod = (float) $productSales
                ->filter(fn ($r) => $r->order?->created_at?->between($periodStart, $periodEnd))
                ->sum('quantity');

            $endingBalance = $currentQty - ($buyAfterEnd - $soldAfterEnd);
            $beginningBalance = $endingBalance - ($buyInPeriod - $soldInPeriod);

            return [
                'product_id' => $productId,
                'product_name' => $productNames[$productId] ?? "Noma'lum",
                'beginning_balance' => $beginningBalance,
                'incoming' => $buyInPeriod,
                'outgoing' => $soldInPeriod,
                'ending_balance' => $endingBalance,
                'current_balance' => $currentQty,
            ];
        })->sortBy('product_name')->values();

        return [
            'warehouse_name' => $warehouse->name,
            'rows' => $rows,
            'summary' => [
                'total_beginning' => (float) $rows->sum('beginning_balance'),
                'total_incoming' => (float) $rows->sum('incoming'),
                'total_outgoing' => (float) $rows->sum('outgoing'),
                'total_ending' => (float) $rows->sum('ending_balance'),
                'total_current' => (float) $rows->sum('current_balance'),
            ],
        ];
    }
}
