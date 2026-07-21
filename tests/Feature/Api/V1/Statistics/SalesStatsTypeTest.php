<?php

namespace Tests\Feature\Api\V1\Statistics;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesStatsTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_sales_is_a_json_number_not_a_string(): void
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'api');

        Order::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'seller_id' => $admin->id,
            'shop_id' => $admin->shop_id,
            'warehouse_id' => Warehouse::factory()->create(['shop_id' => $admin->shop_id])->id,
            'subtotal' => 15000,
            'order_total_price' => 15000,
            'order_total_paid' => 15000,
            'debt_amount' => 0,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/v1/statistics/sales-stats');

        $response->assertStatus(200);

        $row = $response->json('data.sales_stats.0');
        $this->assertNotNull($row, 'Expected at least one sales_stats row');
        $this->assertIsInt($row['total_sales'], 'total_sales must decode as a JSON number, not a string');
    }
}
