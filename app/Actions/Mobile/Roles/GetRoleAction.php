<?php

namespace App\Actions\Mobile\Roles;

use App\Dtos\Mobile\Roles\GetRoleDTO;
use Spatie\Permission\Models\Role;

class GetRoleAction
{
    public function handle(int $id): array
    {
        $role = Role::query()->with('permissions')->find($id);
        error_if($role === null, __('roles.not_found'));

        return [
            'role' => GetRoleDTO::from($role)->toArray(),
        ];
    }
}
