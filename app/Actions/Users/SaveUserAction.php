<?php

namespace App\Actions\Users;

use App\Dtos\Users\SaveUserRequest;
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
