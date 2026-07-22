<?php

namespace App\Actions\Admin\Auth;

use App\Dtos\Admin\Auth\LoginRequest;
use App\Enums\TokenAbility;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function handle(LoginRequest $request): array
    {
        $user = Admin::query()->where(['email' => $request->username])->first();

        error_if($user === null, __('auth.incorrect_credential'));
        error_unless(Hash::check($request->password, $user->password), __('auth.incorrect_credential'));
        return [
            'token' => [
                'access_token' => $user->createToken(
                    name: 'auth_token',
                    abilities: [TokenAbility::ACCESS_API],
                    expiresAt: now()->addMinutes(config('sanctum.expiration'))
                )->plainTextToken,
                'token_type' => 'bearer',
                'expires_in' => config('sanctum.expiration'),
            ],
            'refresh_token' => [
                'refresh_token' => $user->createToken(
                    name: 'refresh_token',
                    abilities: [TokenAbility::ISSUE_ACCESS_TOKEN],
                    expiresAt: now()->addMinutes(config('sanctum.refresh_expiration'))
                )->plainTextToken,
                'token_type' => 'refresh',
                'expires_in' => config('sanctum.refresh_expiration'),
            ]
        ];
    }
}