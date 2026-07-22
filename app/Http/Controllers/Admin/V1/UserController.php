<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Users\DestroyUserAction;
use App\Actions\Admin\Users\FetchUsersAction;
use App\Actions\Admin\Users\GetUserAction;
use App\Actions\Admin\Users\ListUsersAction;
use App\Actions\Admin\Users\SaveUserAction;
use App\Actions\Admin\Users\UpdateUserAction;
use App\Dtos\Admin\Users\SaveUserRequest;
use App\Dtos\Admin\Users\UpdateUserRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class UserController extends ApiBaseController
{
    public function list(Request $request, ListUsersAction $action): array
    {
        return $action->handle($request);
    }

    public function index(Request $request, FetchUsersAction $action): array
    {
        return $action->handle($request);
    }

    public function update(int $id, Request $request, UpdateUserAction $action): string
    {
        return $action->handle($id, UpdateUserRequest::from($request));
    }

    public function show(int $id, GetUserAction $action): array
    {
        return $action->handle($id);
    }

    public function destroy(int $id, DestroyUserAction $action): string
    {
        return $action->handle($id);
    }

    public function store(Request $request, SaveUserAction $action): string
    {
        return $action->handle(SaveUserRequest::from($request));
    }
}
