<?php

namespace App\Http\Controllers\V1;

use App\Actions\Permissions\ListPermissionsAction;
use App\Http\Controllers\ApiBaseController;

class PermissionController extends ApiBaseController
{
    public function list(ListPermissionsAction $action): array
    {
        return $action->handle();
    }
}