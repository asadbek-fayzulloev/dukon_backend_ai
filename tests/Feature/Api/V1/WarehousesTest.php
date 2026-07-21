<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WarehousesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/warehouses');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'name' => 'Test Name',
            'shop_id' => 1,
        ];

        $response = $this->postJson('/api/v1/warehouses', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $warehouse = \App\Models\Warehouse::factory()->create();

        $payload = [
            'name' => 'Test Name',
            'shop_id' => 1,
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'warehouses' . '/' . $warehouse->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
