<?php

namespace App\Actions\Admin\Statistics;

use App\Dtos\Admin\Statistics\SalesStatRequest;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TopProductsStatAction
{
    public function handle(SalesStatRequest $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

        $stats = OrderItem::query()
            ->whereHas('order', fn ($query) => $query
                ->where('company_id', user()->company_id)
                ->whereBetween('created_at', [$fromDate, $toDate]))
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(8)
            ->with('product:id,name')
            ->get()
            ->map(fn ($row) => [
                'product_name' => $row->product->name ?? "Noma'lum",
                'quantity' => (float) $row->total_quantity,
                'revenue' => (float) $row->total_revenue,
            ]);

        return ['top_products_stats' => $stats];
    }
}
