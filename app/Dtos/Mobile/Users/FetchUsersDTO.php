<?php

namespace App\Dtos\Mobile\Users;

use Spatie\LaravelData\Data;

class FetchUsersDTO extends Data
{
    public int $id;
    public ?string $name;
    public string $phone;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'phone' => $this->phone,
        ];

    }
}