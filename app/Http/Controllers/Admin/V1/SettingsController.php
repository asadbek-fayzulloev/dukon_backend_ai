<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Settings\DestroySettingAction;
use App\Actions\Admin\Settings\FetchSettingsAction;
use App\Actions\Admin\Settings\GetSettingAction;
use App\Actions\Admin\Settings\SaveSettingAction;
use App\Actions\Admin\Settings\UpdateSettingAction;
use App\Dtos\Admin\Settings\SaveSettingRequest;
use App\Dtos\Admin\Settings\UpdateSettingRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class SettingsController extends ApiBaseController
{
    public function index(FetchSettingsAction $action): array
    {
        return $action->handle();
    }

    public function show(int $id, GetSettingAction $action): array
    {
        return $action->handle($id);
    }

    public function update(int $id, Request $request, UpdateSettingAction $action): string
    {
        return $action->handle($id, UpdateSettingRequest::from($request));
    }

    public function store(Request $request, SaveSettingAction $action): string
    {
        return $action->handle(SaveSettingRequest::from($request));
    }

    public function destroy(int $id, DestroySettingAction $action): string
    {
        return $action->handle($id);
    }
}
