<?php

namespace App\Dtos\Admin\Settings;

use Spatie\LaravelData\Data;

class GetSettingDTO extends Data
{
    public int $id;
    public string $key;
    public ?string $value;
    public ?string $file;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'file_link' => $this->file
        ];
    }
}
