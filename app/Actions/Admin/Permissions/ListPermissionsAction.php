<?php

namespace App\Actions\Admin\Permissions;


use App\Dtos\Admin\Auth\PermissionDTO;
use Spatie\Permission\Models\Permission;

class ListPermissionsAction
{
    public function handle(): array
    {
        $permissions = Permission::query()->get();
        return [
            'permissions' => PermissionDTO::collect($permissions)
        ];
    }
}