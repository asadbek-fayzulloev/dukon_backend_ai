<?php

namespace App\Actions\Admin\Statistics;

use App\Models\Debt;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class GetStatWidgetAction
{
    public function handle(Request $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        $orderPaymentsQuery = OrderPayment::query()
            ->whereHas('order', fn ($query) => $query->where('company_id', user()->company_id));
        if($fromDate && $toDate){
            $orderPaymentsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $money_in_kassa = $orderPaymentsQuery->sum('payed_price');
        $debt_summ = Debt::query()->where('company_id', user()->company_id)->sum('remaining_amount');
        $all_neat_price = WarehouseProduct::query()->where('company_id', user()->company_id)->selectRaw('SUM(net_price * quantity) as total')->value('total');
        $all_sale_price = WarehouseProduct::query()->where('company_id', user()->company_id)->selectRaw('SUM(price * quantity) as totalp')->value('totalp');
        $today_orders_count = Order::query()
            ->where('company_id', user()->company_id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $low_stock_count = Product::query()
            ->where('company_id', user()->company_id)
            ->whereColumn('quantity', '<=', 'notify_limit')
            ->count();
        return [
            'widgets' => [
                [
                    'name' => 'Jami tan narx',
                    'value' => number_format($all_neat_price) . ' UZS',
                ],
                [
                    'name' => 'Jami sotuv narx',
                    'value' => number_format($all_sale_price) . ' UZS',
                ],
                [
                    'name' => 'Kassadagi pul',
                    'value' => number_format($money_in_kassa) . ' UZS',
                ],
                [
                    'name' => 'Qarzdorlik',
                    'value' => number_format($debt_summ) . ' UZS',
                ],
                [
                    'name' => 'Bugungi buyurtmalar',
                    'value' => number_format($today_orders_count),
                ],
                [
                    'name' => 'Kam qolgan mahsulotlar',
                    'value' => number_format($low_stock_count),
                ],
            ]
        ];
    }
}
