<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $product = \App\Models\Product::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'products' . '/' . $product->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'name' => 'Test Name',
            'net_price' => 1.0,
            'price' => 1.0,
            'quantity' => 1.0,
            'notify_limit' => 1.0,
            'category_id' => 1,
            'unit_id' => 1,
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $product = \App\Models\Product::factory()->create();

        $payload = [
            'name' => 'Test Name',
            'net_price' => 1.0,
            'price' => 1.0,
            'quantity' => 1.0,
            'notify_limit' => 1.0,
            'unit_id' => 1,
            'category_id' => 1,
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'products' . '/' . $product->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_delete(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $product = \App\Models\Product::factory()->create();

        $response = $this->deleteJson('/' . 'api' . '/' . 'v1' . '/' . 'products' . '/' . $product->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
