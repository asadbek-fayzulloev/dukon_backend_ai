<?php

namespace App\Dtos\Mobile\Users;

use Spatie\LaravelData\Data;

class FetchUsersDTO extends Data
{
    public int $id;
    public ?string $name;
    public string $phone;
    public ?float $total_sales;
    public ?float $total_debt;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'phone' => $this->phone,
            'total_sales' => (float) ($this->total_sales ?? 0),
            'total_debt' => (float) ($this->total_debt ?? 0),
        ];

    }
}