<?php

namespace App\Actions\Orders;

use App\Dtos\Orders\GetOrderDTO;
use App\Models\Order;

class GetOrderAction
{
    public function handle(int $id): array
    {
        $order = Order::query()
            ->where('shop_id', user()->shop_id)
            ->with(['items.product', 'payments', 'debt'])
            ->find($id);
        error_if($order === null, __('orders.not-found'));
        return [
            'order' => GetOrderDTO::from($order)->toArray()
        ];
    }
}
