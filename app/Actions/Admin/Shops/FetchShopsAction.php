<?php

namespace App\Actions\Admin\Shops;

use App\Dtos\Admin\Shops\FetchShopDTO;
use App\Models\Shop;

class FetchShopsAction
{
    public function handle(): array
    {
        $shops = Shop::query()->where('company_id', user()->company_id)->orderByDesc('created_at')->get();

        return [
            'shops' => FetchShopDTO::collect($shops),
        ];
    }
}
