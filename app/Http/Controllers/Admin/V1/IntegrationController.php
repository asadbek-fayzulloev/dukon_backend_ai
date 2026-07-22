<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Integrations\GetOneCSettingsAction;
use App\Actions\Admin\Integrations\TestOneCConnectionAction;
use App\Actions\Admin\Integrations\UpdateOneCSettingsAction;
use App\Dtos\Admin\Integrations\UpdateOneCSettingsRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class IntegrationController extends ApiBaseController
{
    public function oneCShow(GetOneCSettingsAction $action): array
    {
        return $action->handle();
    }

    public function oneCUpdate(Request $request, UpdateOneCSettingsAction $action): string
    {
        return $action->handle(UpdateOneCSettingsRequest::from($request));
    }

    public function oneCTest(TestOneCConnectionAction $action): array
    {
        return $action->handle();
    }
}
