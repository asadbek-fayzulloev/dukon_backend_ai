<?php

namespace App\Actions\Mobile\Shops;

use App\Dtos\Mobile\Shops\UpdateShopRequest;
use App\Models\Shop;

class UpdateShopAction
{
    public function handle(int $id, UpdateShopRequest $request): string
    {
        $shop = Shop::find($id);
        error_if($shop === null, __('shops.not_found'));
        $shop->name = $request->name;
        $shop->save();

        return __('shops.updated');
    }
}
