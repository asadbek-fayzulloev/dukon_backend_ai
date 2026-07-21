<?php

namespace Tests\Feature\Api\V1\Warehouses;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouses_list_products(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $warehouse = \App\Models\Warehouse::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'warehouses' . '/' . $warehouse->id . '/' . 'products');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_warehouses_add_product(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $warehouse = \App\Models\Warehouse::factory()->create();

        $payload = [
            'product_id' => 1,
            'price' => 1.0,
            'net_price' => 1.0,
            'amount' => 1.0,
        ];

        $response = $this->postJson('/' . 'api' . '/' . 'v1' . '/' . 'warehouses' . '/' . $warehouse->id . '/' . 'products', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
