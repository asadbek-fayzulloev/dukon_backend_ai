<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_login(): void
    {
        // Seed a user with credentials we control, so we can log in with them
        $admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // skip Hash::make() if your
            // Admin model already casts password => 'hashed'
        ]);

        $payload = [
            'username' => 'admin@admin.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token' => ['access_token', 'token_type', 'expires_in'],
                    'refresh_token' => ['refresh_token', 'token_type', 'expires_in'],
                ],
            ]); // adjust keys to match your actual response shape
    }
}
