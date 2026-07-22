<?php

namespace App\Actions\Admin\Reports;

use App\Dtos\Admin\Reports\ReportDateRangeRequest;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossReportAction
{
    public function handle(ReportDateRangeRequest $request): array
    {
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        $rows = OrderItem::query()
            ->whereHas('order', function ($query) use ($fromDate, $toDate) {
                $query->where('company_id', user()->company_id)
                    ->when(user()->shop_id, fn ($q, $shopId) => $q->where('shop_id', $shopId))
                    ->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->select(
                DB::raw('DATE(order_items.created_at) as date'),
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('SUM(net_price * quantity) as cost')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'revenue' => (float) $row->revenue,
                'cost' => (float) $row->cost,
                'profit' => (float) $row->revenue - (float) $row->cost,
            ]);

        return [
            'rows' => $rows->values(),
            'summary' => [
                'total_revenue' => (float) $rows->sum('revenue'),
                'total_cost' => (float) $rows->sum('cost'),
                'total_profit' => (float) $rows->sum('profit'),
            ],
        ];
    }
}
