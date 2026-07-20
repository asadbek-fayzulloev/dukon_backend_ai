<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
//        dispatch(new PrintJob($product));
    }

    public function creating(Product $product): void
    {
        $product->uzd_price = collect(get_currency())->where('Ccy', 'USD')->first()['Rate'];
    }
}
