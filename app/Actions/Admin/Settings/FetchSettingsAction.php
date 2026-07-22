<?php

namespace App\Actions\Admin\Settings;

use App\Dtos\Admin\Settings\FetchSettingsDTO;
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
