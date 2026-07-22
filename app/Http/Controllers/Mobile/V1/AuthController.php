<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Auth\LoginAction;
use App\Actions\Mobile\Auth\MeAction;
use App\Dtos\Mobile\Auth\LoginRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{
    public function login(Request $request, LoginAction $action): array
    {
        return $action->handle(LoginRequest::from($request));
    }

    public function me(MeAction $action): array
    {
        return $action->handle();
    }
}