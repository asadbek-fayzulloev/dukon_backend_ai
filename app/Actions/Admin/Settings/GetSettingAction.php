<?php

namespace App\Actions\Admin\Settings;

use App\Dtos\Admin\Settings\GetSettingDTO;
use App\Models\Setting;

class GetSettingAction
{
    public function handle(int $id): array
    {
        $setting = Setting::query()->find($id);
        error_if($setting === null, __('settings.not_found'));
        return [
            'setting' => GetSettingDTO::from($setting)->toArray()
        ];
    }
}
