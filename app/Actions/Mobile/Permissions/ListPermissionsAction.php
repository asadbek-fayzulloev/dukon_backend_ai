<?php

namespace App\Actions\Mobile\Permissions;


use App\Dtos\Auth\PermissionDTO;
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