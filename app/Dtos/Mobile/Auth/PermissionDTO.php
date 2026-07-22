<?php

namespace App\Dtos\Mobile\Auth;

use Spatie\LaravelData\Data;

class PermissionDTO extends Data
{
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}