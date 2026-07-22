<?php

namespace App\Actions\Admin\Roles;

use Spatie\Permission\Models\Role;

class DestroyRoleAction
{
    public function handle(int $id): string
    {
        $role = Role::query()->find($id);
        error_if($role === null, __('roles.not_found'));
        $role->delete();

        return __('roles.deleted');
    }
}
