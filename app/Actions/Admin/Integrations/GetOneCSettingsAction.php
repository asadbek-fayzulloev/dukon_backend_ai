<?php

namespace App\Actions\Admin\Integrations;

use App\Models\CompanyIntegration;

class GetOneCSettingsAction
{
    public function handle(): array
    {
        $integration = CompanyIntegration::query()
            ->where('company_id', user()->company_id)
            ->where('provider', 'one_c')
            ->first();

        return [
            'settings' => [
                'url' => $integration?->url,
                'username' => $integration?->username,
                'has_password' => !empty($integration?->password),
                'connected_at' => $integration?->connected_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
