<?php

namespace App\Actions\Admin\Shops;

use App\Models\Shop;

class DestroyShopAction
{
    public function handle(int $id): string
    {
        $shop = Shop::where('company_id', user()->company_id)->find($id);
        error_if($shop === null, __('shops.not_found'));
        $shop->delete();

        return __('shops.deleted');
    }
}
