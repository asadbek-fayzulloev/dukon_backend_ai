<?php

namespace App\Dtos\Mobile\Admins;

use App\Models\Shop;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class FetchAdminDTO extends Data
{
    public int $id;
    public string $name;
    public ?string $email;
    public ?int $shop_id;

    #[LoadRelation]
    public ?Shop $shop;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'shop_id' => $this->shop_id,
            'shop_name' => $this->shop?->name,
        ];
    }
}
