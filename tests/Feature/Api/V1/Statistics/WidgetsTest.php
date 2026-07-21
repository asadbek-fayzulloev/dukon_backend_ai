<?php

namespace Tests\Feature\Api\V1\Statistics;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_widgets(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/statistics/widgets');

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
