<?php

namespace App\Actions\Admin\Statistics;

use App\Dtos\Admin\Statistics\SalesStatRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesStatAction
{
    public function handle(SalesStatRequest $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

        $sales = Order::query()
            ->where('company_id', user()->company_id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(order_total_price) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'sales_stats' => $sales
        ];
    }
}