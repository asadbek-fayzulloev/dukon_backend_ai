<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $key = 'order_items_synced';
        $syncedOrderItems = Cache::get($key) ?? [];
        $orderItems = OrderItem::query()->whereNotIn('id', $syncedOrderItems)->get();

        foreach ($orderItems as $orderItem) {

            $product = $orderItem->product;
            if ($product->quantity > $orderItem->quantity) {
                $product->quantity = $product->quantity - $orderItem->quantity;
                $product->save();
                $syncedOrderItems[] = $orderItem->id;
            }

        }
        Cache::set($key, $syncedOrderItems);
    }
}
