<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Permissions\ListPermissionsAction;
use App\Http\Controllers\ApiBaseController;

class PermissionController extends ApiBaseController
{
    public function list(ListPermissionsAction $action): array
    {
        return $action->handle();
    }
}