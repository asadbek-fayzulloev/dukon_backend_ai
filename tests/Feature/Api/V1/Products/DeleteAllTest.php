<?php

namespace Tests\Feature\Api\V1\Products;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteAllTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_destroy_all(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->deleteJson('/api/v1/products/delete-all');

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
