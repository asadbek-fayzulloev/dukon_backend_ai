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
     * Classic 1C-style "Материальный отчет" (turnover-balance) report for one
     * warehouse: one row per (product, net_price) batch — the same product
     * bought at different costs over time gets its own row, matching how
     * warehouse_products itself buckets stock. ending = beginning + kirim -
     * chiqim, reconstructed backward from the live warehouse_products
     * quantity (the only point-in-time snapshot actually stored) using buy
     * history + sold order_items as the ledger.
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

        $currentBatches = WarehouseProduct::query()
            ->where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->get(['product_id', 'net_price', 'quantity']);

        $buyRows = WarehouseProductHistory::query()
            ->where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'buy')
            ->get(['product_id', 'net_price', 'quantity', 'created_at']);

        $soldRows = OrderItem::query()
            ->whereHas('order', fn ($query) => $query->where('company_id', $companyId)->where('warehouse_id', $warehouseId))
            ->with('order:id,created_at')
            ->get(['id', 'order_id', 'product_id', 'net_price', 'quantity']);

        $batchKey = fn ($productId, $netPrice) => $productId . '|' . number_format((float) $netPrice, 2, '.', '');

        $currentByBatch = [];
        $batchMeta = [];
        foreach ($currentBatches as $row) {
            $key = $batchKey($row->product_id, $row->net_price);
            $currentByBatch[$key] = ($currentByBatch[$key] ?? 0) + (float) $row->quantity;
            $batchMeta[$key] ??= [$row->product_id, (float) $row->net_price];
        }
        foreach ($buyRows as $row) {
            $key = $batchKey($row->product_id, $row->net_price);
            $batchMeta[$key] ??= [$row->product_id, (float) $row->net_price];
        }
        foreach ($soldRows as $row) {
            $key = $batchKey($row->product_id, $row->net_price);
            $batchMeta[$key] ??= [$row->product_id, (float) $row->net_price];
        }

        $productIds = collect($batchMeta)->pluck(0)->unique()->values();
        $products = Product::query()->whereIn('id', $productIds)->with('unit')->get()->keyBy('id');

        $rows = collect($batchMeta)
            ->map(function ($meta) use ($currentByBatch, $buyRows, $soldRows, $periodStart, $periodEnd, $products, $batchKey) {
                [$productId, $netPrice] = $meta;
                $key = $batchKey($productId, $netPrice);
                $currentQty = (float) ($currentByBatch[$key] ?? 0);

                $batchBuys = $buyRows->filter(fn ($r) => (int) $r->product_id === (int) $productId && $batchKey($r->product_id, $r->net_price) === $key);
                $batchSales = $soldRows->filter(fn ($r) => (int) $r->product_id === (int) $productId && $batchKey($r->product_id, $r->net_price) === $key);

                $buyAfterEnd = (float) $batchBuys->filter(fn ($r) => $r->created_at->gt($periodEnd))->sum('quantity');
                $soldAfterEnd = (float) $batchSales
                    ->filter(fn ($r) => $r->order?->created_at?->gt($periodEnd))
                    ->sum('quantity');

                $buyInPeriod = (float) $batchBuys
                    ->filter(fn ($r) => $r->created_at->between($periodStart, $periodEnd))
                    ->sum('quantity');
                $soldInPeriod = (float) $batchSales
                    ->filter(fn ($r) => $r->order?->created_at?->between($periodStart, $periodEnd))
                    ->sum('quantity');

                $endingQty = $currentQty - ($buyAfterEnd - $soldAfterEnd);
                $beginningQty = $endingQty - ($buyInPeriod - $soldInPeriod);

                $product = $products->get($productId);

                return [
                    'product_id' => $productId,
                    'product_name' => $product?->name ?? "Noma'lum",
                    'product_code' => $product?->code,
                    'unit_name' => $product?->unit?->name,
                    'net_price' => $netPrice,
                    'beginning_qty' => $beginningQty,
                    'beginning_sum' => $beginningQty * $netPrice,
                    'incoming_qty' => $buyInPeriod,
                    'incoming_sum' => $buyInPeriod * $netPrice,
                    'outgoing_qty' => $soldInPeriod,
                    'outgoing_sum' => $soldInPeriod * $netPrice,
                    'ending_qty' => $endingQty,
                    'ending_sum' => $endingQty * $netPrice,
                    'current_qty' => $currentQty,
                    'current_sum' => $currentQty * $netPrice,
                ];
            })
            ->filter(fn ($r) => abs($r['beginning_qty']) > 0.0001
                || abs($r['incoming_qty']) > 0.0001
                || abs($r['outgoing_qty']) > 0.0001
                || abs($r['ending_qty']) > 0.0001
                || abs($r['current_qty']) > 0.0001)
            ->sortBy('product_name')
            ->values();

        return [
            'warehouse_name' => $warehouse->name,
            'rows' => $rows,
            'summary' => [
                'total_beginning_qty' => (float) $rows->sum('beginning_qty'),
                'total_beginning_sum' => (float) $rows->sum('beginning_sum'),
                'total_incoming_qty' => (float) $rows->sum('incoming_qty'),
                'total_incoming_sum' => (float) $rows->sum('incoming_sum'),
                'total_outgoing_qty' => (float) $rows->sum('outgoing_qty'),
                'total_outgoing_sum' => (float) $rows->sum('outgoing_sum'),
                'total_ending_qty' => (float) $rows->sum('ending_qty'),
                'total_ending_sum' => (float) $rows->sum('ending_sum'),
                'total_current_qty' => (float) $rows->sum('current_qty'),
                'total_current_sum' => (float) $rows->sum('current_sum'),
            ],
        ];
    }
}
