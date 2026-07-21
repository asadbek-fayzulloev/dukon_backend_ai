<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DebtsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/debts');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $debt = \App\Models\Debt::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'debts' . '/' . $debt->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $debt = \App\Models\Debt::factory()->create();

        $payload = [
            'return_date' => '2026-07-21 17:18:15',
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'debts' . '/' . $debt->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
