<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PriceStatWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jami tan narx', number_format(round(Product::query()->selectRaw('SUM(net_price * quantity) as total')->value('total'), 2)) . ' $'),
            Stat::make('Jami sotuv narx', number_format(round(Product::query()->selectRaw('SUM(price * quantity) as total')->value('total'), 2)) . ' UZS'),
            Stat::make('Kassadagi pul', number_format(round(Order::query()->sum('order_total_paid'), 2)) . ' UZS'),
        ];
    }
}
