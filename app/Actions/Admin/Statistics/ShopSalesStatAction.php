<?php

namespace App\Actions\Admin\Statistics;

use App\Dtos\Admin\Statistics\SalesStatRequest;
use App\Models\Order;
use Carbon\Carbon;

class ShopSalesStatAction
{
    public function handle(SalesStatRequest $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

        $stats = Order::query()
            ->where('company_id', user()->company_id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->with('shop:id,name')
            ->get()
            ->groupBy('shop_id')
            ->map(fn ($orders) => [
                'shop_name' => $orders->first()->shop->name ?? "Noma'lum",
                'total_sales' => (float) $orders->sum('order_total_price'),
            ])
            ->values();

        return ['shop_sales_stats' => $stats];
    }
}
