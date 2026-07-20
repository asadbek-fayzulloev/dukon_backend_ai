<?php

namespace App\Actions\Settings;

use App\Dtos\Settings\FetchSettingsDTO;
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
