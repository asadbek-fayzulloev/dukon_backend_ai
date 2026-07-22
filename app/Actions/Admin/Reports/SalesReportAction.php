<?php

namespace App\Actions\Admin\Reports;

use App\Dtos\Admin\Reports\ReportDateRangeRequest;
use App\Models\Order;
use Carbon\Carbon;

class SalesReportAction
{
    public function handle(ReportDateRangeRequest $request): array
    {
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        $orders = Order::query()
            ->where('company_id', user()->company_id)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->where('shop_id', $shopId))
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->with(['shop:id,name', 'seller:id,name'])
            ->orderBy('created_at')
            ->get();

        $rows = $orders->map(fn ($order) => [
            'id' => $order->id,
            'date' => $order->created_at?->format('Y-m-d H:i'),
            'shop_name' => $order->shop?->name,
            'seller_name' => $order->seller?->name,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'total' => (float) $order->order_total_price,
            'paid' => (float) $order->order_total_paid,
            'debt' => (float) $order->debt_amount,
            'status' => $order->status,
        ]);

        return [
            'rows' => $rows->values(),
            'summary' => [
                'orders_count' => $orders->count(),
                'total_subtotal' => (float) $orders->sum('subtotal'),
                'total_discount' => (float) $orders->sum('discount_amount'),
                'total_sales' => (float) $orders->sum('order_total_price'),
                'total_paid' => (float) $orders->sum('order_total_paid'),
                'total_debt' => (float) $orders->sum('debt_amount'),
            ],
        ];
    }
}
