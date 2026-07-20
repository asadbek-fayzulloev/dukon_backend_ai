<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CorrectProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:correct-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::query()->get();
        $usd_price = collect(get_currency())->where('Ccy', 'USD')->first()['Rate'];
        $usd_price = ceil($usd_price / 1000) * 1000;

        foreach ($products as $product) {
            $product->net_price_usd = $product->net_price * $usd_price;
            $product->save();
        }
    }
}
