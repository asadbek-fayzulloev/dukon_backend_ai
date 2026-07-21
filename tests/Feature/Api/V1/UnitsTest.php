<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/units');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'name' => 'Test Name',
        ];

        $response = $this->postJson('/api/v1/units', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $unit = \App\Models\Unit::factory()->create();

        $payload = [
            'name' => 'Test Name',
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'units' . '/' . $unit->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_delete(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $unit = \App\Models\Unit::factory()->create();

        $response = $this->deleteJson('/' . 'api' . '/' . 'v1' . '/' . 'units' . '/' . $unit->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
