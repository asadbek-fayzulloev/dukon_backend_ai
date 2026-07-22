<?php

namespace App\Actions\Admin\Integrations;

use App\Models\CompanyIntegration;
use App\Services\OneCService;

class TestOneCConnectionAction
{
    public function __construct(private readonly OneCService $service)
    {
    }

    public function handle(): array
    {
        $integration = CompanyIntegration::query()
            ->where('company_id', user()->company_id)
            ->where('provider', 'one_c')
            ->first();

        if ($integration === null) {
            return ['success' => false, 'message' => "Avval 1C sozlamalarini saqlang."];
        }

        $result = $this->service->testConnection($integration);

        if ($result['success']) {
            $integration->connected_at = now();
            $integration->save();
        }

        return $result;
    }
}
