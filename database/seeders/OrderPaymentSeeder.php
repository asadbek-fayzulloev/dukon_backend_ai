<?php

namespace Database\Seeders;

use App\Models\OrderPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderPayment::query()->create([
            'payment_type'=>'click',
            'payed_price'=>'50',
            'order_id'=>'1',
        ]);
    }
}
