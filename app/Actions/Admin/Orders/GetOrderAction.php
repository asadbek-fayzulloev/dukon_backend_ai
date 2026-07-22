<?php

namespace App\Actions\Admin\Orders;

use App\Dtos\Admin\Orders\GetOrderDTO;
use App\Models\Order;

class GetOrderAction
{
    public function handle(int $id): array
    {
        $order = Order::query()
            ->where('company_id', user()->company_id)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->where('shop_id', $shopId))
            ->with(['items.product', 'payments', 'debt'])
            ->find($id);
        error_if($order === null, __('orders.not-found'));
        return [
            'order' => GetOrderDTO::from($order)->toArray()
        ];
    }
}
