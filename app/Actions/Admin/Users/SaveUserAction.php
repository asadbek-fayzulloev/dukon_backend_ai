<?php

namespace App\Actions\Admin\Users;

use App\Dtos\Admin\Users\SaveUserRequest;
use App\Models\User;

class SaveUserAction
{
    public function handle(SaveUserRequest $request): string
    {
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();
        return __('users.stored');
    }
}
