<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Settings\DestroySettingAction;
use App\Actions\Mobile\Settings\FetchSettingsAction;
use App\Actions\Mobile\Settings\GetSettingAction;
use App\Actions\Mobile\Settings\SaveSettingAction;
use App\Actions\Mobile\Settings\UpdateSettingAction;
use App\Dtos\Mobile\Settings\SaveSettingRequest;
use App\Dtos\Mobile\Settings\UpdateSettingRequest;
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
