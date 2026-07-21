<?php

namespace Tests\Feature\Api\V1\Products;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LowStockExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_low_stock_export(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/products/low-stock-export');

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
