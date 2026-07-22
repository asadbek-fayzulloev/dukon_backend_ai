<?php

namespace App\Actions\Mobile\Settings;

use App\Dtos\Mobile\Settings\UpdateSettingRequest;
use App\Models\Setting;

class UpdateSettingAction
{
    public function handle(int $id, UpdateSettingRequest $request): string
    {
        $setting = Setting::query()->find($id);
        error_if($setting === null, __('settings.not_found'));
        $setting->key = $request->key ?? $setting->key;
        $setting->value = $request->value ?? $setting->value;
        if ($request->file !== null) {
            $setting->file = s3_file_upload(request()->file(key: 'file'), path: 'settings');
        }
        $setting->save();
        return __('settings.updated');
    }
}
