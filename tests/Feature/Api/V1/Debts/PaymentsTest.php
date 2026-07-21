<?php

namespace Tests\Feature\Api\V1\Debts;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_debts_debts_pay(): void
    {
        Sanctum::actingAs(Admin::factory()->create(), ['*'], 'api');

        $debt = \App\Models\Debt::factory()->create();

        $payload = [
            'amount' => 1,
            'payment_type' => 'Test value',
            'paid_at' => '2026-07-21 17:18:15',
        ];

        $response = $this->postJson('/' . 'api' . '/' . 'v1' . '/' . 'debts' . '/' . $debt->id . '/' . 'payments', $payload);

        $response->assertStatus(200); // TODO: adjust if needed
    }
}
