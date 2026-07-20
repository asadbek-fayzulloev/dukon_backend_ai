<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CorrectPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:correct-price-command';

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
        $products = Product::query()->where('net_price', '>', 1000)->get();
        foreach ($products as $product) {
            $product->net_price = $product->net_price / 13000;
            $product->save();
        }
    }
}
