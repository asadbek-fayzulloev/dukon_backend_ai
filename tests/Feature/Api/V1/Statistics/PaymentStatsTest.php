<?php

namespace Tests\Feature\Api\V1\Statistics;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_payment_stat(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $response = $this->getJson('/api/v1/statistics/payment-stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'payment_stats' => [['payment_type', 'label', 'amount']],
                ],
            ]);
    }
}
