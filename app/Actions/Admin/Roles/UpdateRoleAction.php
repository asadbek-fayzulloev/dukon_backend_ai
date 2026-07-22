<?php

namespace App\Actions\Admin\Roles;

use App\Dtos\Admin\Roles\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    public function handle(int $id, UpdateRoleRequest $request): string
    {
        $role = Role::query()->find($id);
        error_if($role === null, __('roles.not_found'));

        $role->name = $request->name;
        $role->save();
        $role->syncPermissions(Permission::query()->whereIn('id', $request->permission_ids)->get());

        return __('roles.updated');
    }
}
