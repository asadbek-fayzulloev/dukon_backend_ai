<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SaleRate extends ChartWidget
{
    protected static ?string $heading = 'Oylik sotuvchilar reytingi';

    protected function getData(): array
    {
        $sales = Order::with('seller:id,name')
        ->whereMonth('created_at', now()->month)
        ->get()
            ->groupBy('seller_id')
            ->map(function ($orders, $sellerId) {
                return [
                    'seller_name' => $orders->first()->seller->name ?? 'Noma\'lum',
                    'total_sales' => $orders->count(),
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Mahsulot sotuvlari',
                    'data' => $sales->pluck('total_sales')->toArray(),
                ],
            ],
            'labels' => $sales->pluck('seller_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
