<?php

namespace App\Actions\Mobile\Users;

use App\Models\User;

class DestroyUserAction
{
    public function handle(int $id): string
    {
        $user = User::query()->find($id);
        error_if($user === null, __('users.not-found'));
        $user->delete();
        return __('users.deleted');
    }
}
