<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Roles\DestroyRoleAction;
use App\Actions\Mobile\Roles\FetchRolesAction;
use App\Actions\Mobile\Roles\GetRoleAction;
use App\Actions\Mobile\Roles\SaveRoleAction;
use App\Actions\Mobile\Roles\UpdateRoleAction;
use App\Dtos\Mobile\Roles\SaveRoleRequest;
use App\Dtos\Mobile\Roles\UpdateRoleRequest;
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
