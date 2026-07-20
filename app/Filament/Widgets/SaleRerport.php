<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SaleRerport extends ChartWidget
{

    protected static ?string $heading = 'Sotuvlar Hisoboti';

    protected function getData(): array
    {
        $filter = request()->input('time_filter', 'monthly');

        $dateRange = $this->getDateRange($filter);

        $sales = OrderItem::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(created_at) as date, SUM(product_price * quantity) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jami Sotuvlar',
                    'data' => $sales->pluck('total_sales')->toArray(),
//                    'borderColor' => '#4CAF50',
                ],
            ],
            'labels' => $sales->pluck('date')->toArray(),
        ];
    }

    private function getDateRange($filter)
    {
        switch ($filter) {
            case 'weekly':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek(),
                ];
            case 'yearly':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear(),
                ];
            case 'monthly':
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                ];
        }
    }
    protected function getFilters(): array
    {
        return [
            'monthly' => 'Oylik',
            'weekly' => 'Haftalik',
            'yearly' => 'Yillik',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
