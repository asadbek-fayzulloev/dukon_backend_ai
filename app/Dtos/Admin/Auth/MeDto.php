<?php

namespace App\Dtos\Admin\Auth;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class MeDto extends Data
{
    #[LoadRelation]
    public ?Collection $roles;
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'roles' => RoleDTO::collect($this->roles)->toArray() ?? [],
        ];
    }
}