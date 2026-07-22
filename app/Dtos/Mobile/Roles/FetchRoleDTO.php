<?php

namespace App\Dtos\Mobile\Roles;

use Spatie\LaravelData\Data;

class FetchRoleDTO extends Data
{
    public int $id;
    public string $name;
    public int $permissions_count;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions_count' => $this->permissions_count,
        ];
    }
}
