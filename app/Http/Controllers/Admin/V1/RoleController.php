<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Roles\DestroyRoleAction;
use App\Actions\Admin\Roles\FetchRolesAction;
use App\Actions\Admin\Roles\GetRoleAction;
use App\Actions\Admin\Roles\SaveRoleAction;
use App\Actions\Admin\Roles\UpdateRoleAction;
use App\Dtos\Admin\Roles\SaveRoleRequest;
use App\Dtos\Admin\Roles\UpdateRoleRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class RoleController extends ApiBaseController
{
    public function index(FetchRolesAction $action): array
    {
        return $action->handle();
    }

    public function show(int $id, GetRoleAction $action): array
    {
        return $action->handle($id);
    }

    public function store(Request $request, SaveRoleAction $action): string
    {
        return $action->handle(SaveRoleRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateRoleAction $action): string
    {
        return $action->handle($id, UpdateRoleRequest::from($request));
    }

    public function destroy(int $id, DestroyRoleAction $action): string
    {
        return $action->handle($id);
    }
}
