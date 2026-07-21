<?php

namespace Tests\Feature\Api\V1\WarehouseProducts;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_products_import(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'products' => [],
        ];

        $response = $this->postJson('/api/v1/warehouse-products/import', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
