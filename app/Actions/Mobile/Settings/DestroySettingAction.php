<?php

namespace App\Actions\Mobile\Settings;

use App\Models\Setting;

class DestroySettingAction
{
    public function handle(int $id): string
    {
        $setting = Setting::query()->find($id);
        error_if($setting === null, __('settings.not_found'));
        $setting->delete();
        return __('settings.deleted');
    }
}
