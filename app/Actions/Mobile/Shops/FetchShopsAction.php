<?php

namespace App\Actions\Mobile\Shops;

use App\Dtos\Mobile\Shops\FetchShopDTO;
use App\Models\Shop;

class FetchShopsAction
{
    public function handle(): array
    {
        $shops = Shop::query()->orderByDesc('created_at')->get();

        return [
            'shops' => FetchShopDTO::collect($shops),
        ];
    }
}
