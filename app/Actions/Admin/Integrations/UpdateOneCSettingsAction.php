<?php

namespace App\Actions\Admin\Integrations;

use App\Dtos\Admin\Integrations\UpdateOneCSettingsRequest;
use App\Models\CompanyIntegration;

class UpdateOneCSettingsAction
{
    public function handle(UpdateOneCSettingsRequest $request): string
    {
        $integration = CompanyIntegration::query()->firstOrNew([
            'company_id' => user()->company_id,
            'provider' => 'one_c',
        ]);

        $integration->url = $request->one_c_url;
        $integration->username = $request->one_c_username;
        if (!empty($request->one_c_password)) {
            $integration->password = $request->one_c_password;
        }
        $integration->save();

        return __('integrations.saved');
    }
}
