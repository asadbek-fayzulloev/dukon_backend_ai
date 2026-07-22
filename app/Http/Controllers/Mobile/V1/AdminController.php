<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Admins\DestroyAdminAction;
use App\Actions\Mobile\Admins\FetchAdminsAction;
use App\Actions\Mobile\Admins\GetAdminAction;
use App\Actions\Mobile\Admins\SaveAdminAction;
use App\Actions\Mobile\Admins\UpdateAdminAction;
use App\Dtos\Mobile\Admins\SaveAdminRequest;
use App\Dtos\Mobile\Admins\UpdateAdminRequest;
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
