<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Debt;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalOrderPriceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jami sotuvlar', number_format(Order::query()->sum('order_total_price')) . ' UZS'),
            Stat::make('Jami to`langan summa', number_format(Order::query()->sum('order_total_paid')) . ' UZS'),
            Stat::make('Jami qarzlar', number_format(Debt::query()->sum('amount')) . ' UZS'),

        ];
    }
}
