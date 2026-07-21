<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StaticDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/static-data');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'error' => null,
            ])
            ->assertJsonStructure([
                'data' => [
                    'payment_types' => [['value', 'label']],
                    'discount_types' => [['value', 'label']],
                    'order_statuses' => [['value', 'label']],
                    'debt_statuses' => [['value', 'label']],
                ],
            ]);

        $response->assertJsonFragment(['value' => 'cash', 'label' => 'Naqd'])
            ->assertJsonFragment(['value' => 'transfer', 'label' => 'Pul o‘tkazmasi']);
    }
}
