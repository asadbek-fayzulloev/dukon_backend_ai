<?php

namespace App\Http\Controllers\V1;

use App\Actions\Admins\DestroyAdminAction;
use App\Actions\Admins\FetchAdminsAction;
use App\Actions\Admins\GetAdminAction;
use App\Actions\Admins\SaveAdminAction;
use App\Actions\Admins\UpdateAdminAction;
use App\Dtos\Admins\SaveAdminRequest;
use App\Dtos\Admins\UpdateAdminRequest;
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
