<?php

namespace Tests\Feature\Api\V1;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_update(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $user = \App\Models\User::factory()->create();

        $payload = [
            'name' => 'Test Name',
            'phone' => '+998901234567',
        ];

        $response = $this->putJson('/' . 'api' . '/' . 'v1' . '/' . 'users' . '/' . $user->id, $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_show(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $user = \App\Models\User::factory()->create();

        $response = $this->getJson('/' . 'api' . '/' . 'v1' . '/' . 'users' . '/' . $user->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_delete(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $user = \App\Models\User::factory()->create();

        $response = $this->deleteJson('/' . 'api' . '/' . 'v1' . '/' . 'users' . '/' . $user->id);

        $response->assertStatus(200); // TODO: adjust if needed
    }

    public function test_create(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $payload = [
            'name' => 'Test Name',
            'phone' => '+998901234567',
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
