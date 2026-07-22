<?php

namespace App\Actions\Mobile\WarehouseProducts;

use App\Dtos\Mobile\WarehouseProducts\FetchProductMovementsRequest;
use App\Models\OrderItem;
use App\Models\WarehouseProductHistory;

class FetchProductMovementsAction
{
    /**
     * Merges two independent sources into one chronological ledger:
     * buy/transfer from warehouse_product_histories (see ImportProductAction)
     * and sold from order_items (already written by SaveOrderAction, which
     * this deliberately does not touch — no new stock-out log, just reads
     * the checkout path's existing records).
     */
    public function handle(FetchProductMovementsRequest $request): array
    {
        $histories = WarehouseProductHistory::query()
            ->where('product_id', $request->product_id)
            ->whereHas('warehouse', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->when($request->warehouse_id, fn ($query, $warehouseId) => $query->where('warehouse_id', $warehouseId))
            ->with('admin')
            ->get()
            ->map(fn ($row) => [
                'type' => $row->type,
                'date' => $row->created_at?->toIso8601String(),
                'quantity' => (float) $row->quantity,
                'price' => (float) $row->price,
                'net_price' => (float) $row->net_price,
                'by' => $row->admin?->name,
                'order_id' => null,
            ]);

        $sales = OrderItem::query()
            ->where('product_id', $request->product_id)
            ->whereHas('order', function ($query) use ($request) {
                $query->where('shop_id', user()->shop_id);
                if ($request->warehouse_id) {
                    $query->where('warehouse_id', $request->warehouse_id);
                }
            })
            ->with('order.seller')
            ->get()
            ->map(fn ($row) => [
                'type' => 'sold',
                'date' => $row->created_at?->toIso8601String(),
                'quantity' => (float) $row->quantity,
                'price' => (float) $row->product_price,
                'net_price' => (float) $row->net_price,
                'by' => $row->order?->seller?->name,
                'order_id' => $row->order_id,
            ]);

        $movements = $histories->concat($sales)
            ->sortByDesc('date')
            ->values();

        return ['movements' => $movements->all()];
    }
}
