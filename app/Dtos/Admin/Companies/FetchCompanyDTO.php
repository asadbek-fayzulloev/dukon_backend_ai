<?php

namespace App\Dtos\Admin\Companies;

use Spatie\LaravelData\Data;

class FetchCompanyDTO extends Data
{
    public int $id;
    public string $name;
    public bool $is_active;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
        ];
    }
}
