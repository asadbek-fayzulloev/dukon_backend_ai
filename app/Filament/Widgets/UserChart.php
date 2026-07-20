<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Symfony\Component\Yaml\Tag\TaggedValue;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'Foydalanuvchilar';

    protected function getData(): array
    {

        $user = Trend::model(User::class)
            ->between(
                start: now()->subMonth(12),
                end: now(),
            )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Foydalanuvchilar',
                    'data' => $user->map(fn(TrendValue $value) => $value->aggregate),
//                    'backgroundColor' => '#ff05da',
                    'fill' => false,
                    'borderColor' => '#82006c',
                ],

            ],
            'labels' => $user->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
