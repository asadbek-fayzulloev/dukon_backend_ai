<?php

namespace App\Actions\Shops;

use App\Dtos\Shops\FetchShopDTO;
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
