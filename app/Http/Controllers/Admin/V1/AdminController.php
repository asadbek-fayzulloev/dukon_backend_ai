<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Admins\DestroyAdminAction;
use App\Actions\Admin\Admins\FetchAdminsAction;
use App\Actions\Admin\Admins\GetAdminAction;
use App\Actions\Admin\Admins\SaveAdminAction;
use App\Actions\Admin\Admins\UpdateAdminAction;
use App\Dtos\Admin\Admins\SaveAdminRequest;
use App\Dtos\Admin\Admins\UpdateAdminRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class AdminController extends ApiBaseController
{
    public function index(Request $request, FetchAdminsAction $action): array
    {
        return $action->handle($request);
    }

    public function show(int $id, GetAdminAction $action): array
    {
        return $action->handle($id);
    }

    public function store(Request $request, SaveAdminAction $action): string
    {
        return $action->handle(SaveAdminRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateAdminAction $action): string
    {
        return $action->handle($id, UpdateAdminRequest::from($request));
    }

    public function destroy(int $id, DestroyAdminAction $action): string
    {
        return $action->handle($id);
    }
}
