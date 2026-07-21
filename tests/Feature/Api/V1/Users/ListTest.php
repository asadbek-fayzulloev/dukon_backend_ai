<?php

namespace Tests\Feature\Api\V1\Users;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_list(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/users/list');

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
