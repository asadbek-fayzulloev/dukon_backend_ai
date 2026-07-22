<?php

namespace App\Actions\Mobile\Users;

use App\Dtos\Mobile\Users\GetUserDTO;
use App\Models\User;

class GetUserAction
{
    public function handle(int $id): array
    {
        $user = User::query()->find($id);
        error_if($user === null, __('users.not_found'));

        return [
            'user' => GetUserDTO::from($user)
        ];
    }
}