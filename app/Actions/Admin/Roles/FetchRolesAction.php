<?php

namespace App\Actions\Admin\Roles;

use App\Dtos\Admin\Roles\FetchRoleDTO;
use Spatie\Permission\Models\Role;

class FetchRolesAction
{
    public function handle(): array
    {
        $roles = Role::query()->withCount('permissions')->orderByDesc('created_at')->get();

        return [
            'roles' => array_map(
                fn ($role) => FetchRoleDTO::from($role)->toArray(),
                $roles->all()
            ),
        ];
    }
}
