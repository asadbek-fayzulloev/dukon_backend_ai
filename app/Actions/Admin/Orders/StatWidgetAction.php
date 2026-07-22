<?php

namespace App\Actions\Admin\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class StatWidgetAction
{
    public function handle(Request $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;
        $orderQuery = Order::query()->where('company_id', user()->company_id);

        if($fromDate && $toDate){
            $orderQuery = $orderQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $debts = $orderQuery->sum(DB::raw('(order_total_price - order_total_paid)'));
        $total_paid = $orderQuery->sum(DB::raw('order_total_paid'));
        $total_sales = $orderQuery->sum('order_total_price');
        return [
            'statistics' => [
                'total_sales' => number_format($total_sales) . ' UZS',
                'total_paid' => number_format($total_paid) . ' UZS',
                'total_debts' => number_format($debts) . ' UZS'
            ]
        ];
    }
}