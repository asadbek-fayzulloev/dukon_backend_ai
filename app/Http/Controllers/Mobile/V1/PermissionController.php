<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Permissions\ListPermissionsAction;
use App\Http\Controllers\ApiBaseController;

class PermissionController extends ApiBaseController
{
    public function list(ListPermissionsAction $action): array
    {
        return $action->handle();
    }
}