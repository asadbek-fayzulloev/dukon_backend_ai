<?php

namespace App\Dtos\Admin\Companies;

use Spatie\LaravelData\Data;

class FetchCompanyDTO extends Data
{
    public int $id;
    public string $name;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
