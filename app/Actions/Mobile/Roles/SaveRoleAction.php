<?php

namespace App\Actions\Mobile\Roles;

use App\Dtos\Mobile\Roles\SaveRoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SaveRoleAction
{
    public function handle(SaveRoleRequest $request): string
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);
        $role->syncPermissions(Permission::query()->whereIn('id', $request->permission_ids)->get());

        return __('roles.stored');
    }
}
