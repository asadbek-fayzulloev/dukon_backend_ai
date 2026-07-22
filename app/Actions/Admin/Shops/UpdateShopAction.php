<?php

namespace App\Actions\Admin\Shops;

use App\Dtos\Admin\Shops\UpdateShopRequest;
use App\Models\Shop;

class UpdateShopAction
{
    public function handle(int $id, UpdateShopRequest $request): string
    {
        $shop = Shop::where('company_id', user()->company_id)->find($id);
        error_if($shop === null, __('shops.not_found'));
        $shop->name = $request->name;
        $shop->save();

        return __('shops.updated');
    }
}
