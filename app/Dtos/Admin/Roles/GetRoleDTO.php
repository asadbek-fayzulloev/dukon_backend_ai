<?php

namespace App\Dtos\Admin\Roles;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class GetRoleDTO extends Data
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
            'permissions' => $this->permissions?->map(fn ($p): array => [
                'id' => $p->id,
                'name' => $p->name,
            ])->values()->all() ?? [],
        ];
    }
}
