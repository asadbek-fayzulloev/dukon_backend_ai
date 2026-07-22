<?php

namespace App\Actions\Mobile\Permissions;


use App\Dtos\Mobile\Auth\PermissionDTO;
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