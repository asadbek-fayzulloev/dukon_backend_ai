<?php

namespace Tests\Feature\Api\V1\Statistics;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentStatsTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_is_a_json_number_not_a_string(): void
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'api');

        $order = Order::create([
            'uuid' => (string) Str::uuid(),
            'seller_id' => $admin->id,
            'shop_id' => $admin->shop_id,
            'warehouse_id' => Warehouse::factory()->create(['shop_id' => $admin->shop_id])->id,
            'subtotal' => 15000,
            'order_total_price' => 15000,
            'order_total_paid' => 15000,
            'debt_amount' => 0,
            'status' => 'completed',
        ]);

        OrderPayment::create([
            'order_id' => $order->id,
            'payment_type' => 'cash',
            'payed_price' => 15000,
        ]);

        $response = $this->getJson('/api/v1/statistics/payment-stats');

        $response->assertStatus(200);

        $stats = collect($response->json('data.payment_stats'));
        $cash = $stats->firstWhere('payment_type', 'cash');

        $this->assertNotNull($cash);
        $this->assertIsInt($cash['amount'], 'amount must decode as a JSON number, not a string');
        $this->assertSame(15000, $cash['amount']);
    }
}
