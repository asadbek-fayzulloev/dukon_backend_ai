<?php

namespace App\Actions\Mobile\Shops;

use App\Dtos\Mobile\Shops\SaveShopRequest;
use App\Models\Shop;

class SaveShopAction
{
    public function handle(SaveShopRequest $request): string
    {
        $shop = new Shop();
        $shop->name = $request->name;
        $shop->save();

        return __('shops.stored');
    }
}
