<?php

namespace App\Actions\Admin\Statistics;

use App\Models\Debt;
use App\Models\OrderPayment;
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
            ]
        ];
    }
}
