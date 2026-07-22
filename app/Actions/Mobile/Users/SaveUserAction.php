<?php

namespace App\Actions\Mobile\Users;

use App\Dtos\Mobile\Users\SaveUserRequest;
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
