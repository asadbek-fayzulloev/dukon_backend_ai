<?php

namespace App\Actions\Settings;

use App\Dtos\Settings\SaveSettingRequest;
use App\Models\Setting;

class SaveSettingAction
{
    public function handle(SaveSettingRequest $request): string
    {
        $setting = new Setting();
        $setting->key = $request->key;
        $setting->value = $request->value;
        if ($request->file !== null) {
            $setting->file = s3_file_upload(request()->file(key: 'file'), path: 'settings');
        }
        $setting->save();
        return __('settings.stored');
    }
}
