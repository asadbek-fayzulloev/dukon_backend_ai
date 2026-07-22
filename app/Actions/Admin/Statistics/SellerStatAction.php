<?php

namespace App\Actions\Admin\Statistics;


use App\Dtos\Admin\Statistics\SellerStatRequest;
use App\Models\Order;
use Carbon\Carbon;

class SellerStatAction
{
    public function handle(SellerStatRequest $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

        return [
            'seller_stats' => Order::with('seller:id,name')
                ->where('company_id', user()->company_id)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get()
                ->groupBy('seller_id')
                ->map(function ($orders, $sellerId) {
                    return [
                        'seller_name' => $orders->first()->seller->name ?? 'Noma\'lum',
                        'total_sales' => $orders->count(),
                    ];
                })->values()
        ];


    }
}