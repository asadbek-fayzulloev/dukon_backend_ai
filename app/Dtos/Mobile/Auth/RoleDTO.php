<?php

namespace App\Dtos\Mobile\Auth;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class RoleDTO extends Data
{
    public int $id;
    public string $name;
    #[LoadRelation]
    public ?Collection $permissions;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => PermissionDTO::collect($this->permissions)->toArray() ?? []
        ];
    }
}