<?php

namespace App\Actions\Admin\Users;

use App\Dtos\Admin\Users\GetUserDTO;
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