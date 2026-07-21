<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $order = \App\Models\Order::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'orders' . '/' . $order->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'items' => [],
            'payments' => [],
            'uuid' => '9fd2b017-48e4-42de-9951-737372a4f9cb',
            'warehouse_id' => 1,
            'device_id' => 'Test value',
            'discount_type' => 'Test value',
            'discount_value' => 1,
            'user_id' => 1,
            'user' => [
                'phone' => '+998901234567',
                'name' => 'Test Name',
            ],
            'debt_return_date' => '2026-07-21 17:18:15',
            'sold_at' => '2026-07-21 17:18:15',
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
