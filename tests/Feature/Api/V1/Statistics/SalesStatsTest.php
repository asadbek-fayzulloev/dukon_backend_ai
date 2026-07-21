<?php

namespace Tests\Feature\Api\V1\Statistics;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_sales_stat(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/statistics/sales-stats');

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
