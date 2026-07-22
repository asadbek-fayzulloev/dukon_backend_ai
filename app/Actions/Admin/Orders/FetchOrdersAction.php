<?php

namespace App\Actions\Admin\Orders;

use App\Dtos\Admin\Orders\FetchOrderRequest;
use App\Dtos\Admin\Orders\FetchOrdersDTO;
use App\Dtos\PaginationDTO;
use App\Filters\OrderFilter;
use App\Models\Order;

class FetchOrdersAction
{
    public function handle(FetchOrderRequest $request): array
    {
        $query = Order::query()
                    ->where('company_id', user()->company_id)
                    ->when(user()->shop_id, fn ($query, $shopId) => $query->where('shop_id', $shopId))
                    ->with(['user', 'seller'])
                    ->orderByDesc('created_at');
        $query = (new OrderFilter($query))->apply();
        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $orders = array_map(
            fn($order) => FetchOrdersDTO::from($order)->toArray(),
            $paginator->items()
        );
        return [
            'orders' => $orders,
            'paginator' => new PaginationDTO($paginator),
            'now' => now()
        ];
    }
}
