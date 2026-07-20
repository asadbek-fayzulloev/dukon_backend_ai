<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sotuvlar';

    protected function getData(): array
    {
        $trend = Trend::model(Order::class)
        ->between(
            start: now()->subMonth(12),
            end: now(),
        )
        ->perMonth()
        ->count();

            return [
                'datasets' => [
                    [
                        'label' => 'Sotuvlar',
//                        'backgroundColor' => '#16A351',
                        'borderColor' => '#085227',
                        'fill' => false,
                        'data' => $trend->map(fn (TrendValue $value)=> $value->aggregate),
//                        'data' => Order::query()->pluck('quantity')->groupBy('shop_id'),
                    ],
                ],
                'labels' => $trend->map(fn (TrendValue $value)=> $value->date),
            ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
