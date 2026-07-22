<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Users\DestroyUserAction;
use App\Actions\Mobile\Users\FetchUsersAction;
use App\Actions\Mobile\Users\GetUserAction;
use App\Actions\Mobile\Users\ListUsersAction;
use App\Actions\Mobile\Users\SaveUserAction;
use App\Actions\Mobile\Users\UpdateUserAction;
use App\Dtos\Mobile\Users\SaveUserRequest;
use App\Dtos\Mobile\Users\UpdateUserRequest;
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
