<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/product-categories');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $productCategory = \App\Models\ProductCategory::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'product-categories' . '/' . $productCategory->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'name' => 'Test Name',
        ];

        $response = $this->postJson('/api/v1/product-categories', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $productCategory = \App\Models\ProductCategory::factory()->create();

        $payload = [
            'name' => 'Test Name',
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'product-categories' . '/' . $productCategory->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_delete(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $productCategory = \App\Models\ProductCategory::factory()->create();

        $response = $this->deleteJson('/' . 'api' . '/' . 'v1' . '/' . 'product-categories' . '/' . $productCategory->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
