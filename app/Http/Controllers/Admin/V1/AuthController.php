<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Auth\LoginAction;
use App\Actions\Admin\Auth\MeAction;
use App\Actions\Admin\Auth\RegisterAction;
use App\Dtos\Admin\Auth\LoginRequest;
use App\Dtos\Admin\Auth\RegisterRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{
    public function login(Request $request, LoginAction $action): array
    {
        return $action->handle(LoginRequest::from($request));
    }

    public function register(Request $request, RegisterAction $action): string
    {
        return $action->handle(RegisterRequest::from($request));
    }

    public function me(MeAction $action): array
    {
        return $action->handle();
    }
}