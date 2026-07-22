<?php

namespace App\Actions\Admin\Users;

use App\Dtos\Admin\Users\UpdateUserRequest;
use App\Models\User;

class UpdateUserAction
{
    public function handle(int $id, UpdateUserRequest $request): string
    {
        $user = User::query()->find($id);
        error_if($user === null, __('users.not_found'));
        $user->name = $request->name ?? $user->name;
        $user->phone = $request->phone ?? $user->phone;
        $user->save();
        return __('users.updated');
    }
}