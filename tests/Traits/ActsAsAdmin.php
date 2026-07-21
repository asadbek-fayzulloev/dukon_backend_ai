<?php

namespace Tests\Traits;

use App\Models\Admin;
use Laravel\Sanctum\Sanctum;

/**
 * Drop this into any Feature test that hits an authenticated endpoint:
 *
 *   use Tests\Traits\ActsAsAdmin;
 *
 *   class DebtsTest extends TestCase
 *   {
 *       use ActsAsAdmin;
 *
 *       public function test_index(): void
 *       {
 *           $this->authenticateAdmin();
 *           $response = $this->getJson('/api/v1/debts');
 *           $response->assertStatus(200);
 *       }
 *   }
 *
 * Adjust the Admin model / factory / ability names below to match your app.
 */
trait ActsAsAdmin
{
    protected function authenticateAdmin(array $attributes = [], array $abilities = ['*']): Admin
    {
        $admin = Admin::factory()->create($attributes);

        Sanctum::actingAs($admin, $abilities, 'api');

        return $admin;
    }
}
