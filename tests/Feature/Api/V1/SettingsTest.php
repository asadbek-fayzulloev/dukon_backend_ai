<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/settings');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $setting = \App\Models\Setting::factory()->create();

        $payload = [
            'key' => 'Test value',
            'value' => 'Test value',
            'file' => [],
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'settings' . '/' . $setting->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'key' => 'Test value',
            'value' => 'Test value',
            'file' => [],
        ];

        $response = $this->postJson('/api/v1/settings', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $setting = \App\Models\Setting::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'settings' . '/' . $setting->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_delete(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $setting = \App\Models\Setting::factory()->create();

        $response = $this->deleteJson('/' . 'api' . '/' . 'v1' . '/' . 'settings' . '/' . $setting->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
