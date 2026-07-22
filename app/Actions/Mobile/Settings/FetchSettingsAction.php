<?php

namespace App\Actions\Mobile\Settings;

use App\Dtos\Mobile\Settings\FetchSettingsDTO;
use App\Models\Setting;

class FetchSettingsAction
{
    public function handle(): array
    {
        $settings = Setting::query()->get();
        return [
            'settings' => FetchSettingsDTO::collect($settings)->toArray()
        ];
    }
}
