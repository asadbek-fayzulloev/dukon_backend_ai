<?php

namespace App\Dtos\Admin\Admins;

use App\Models\Shop;
use Illuminate\Support\Collection;
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

    #[LoadRelation]
    public ?Collection $roles;

    public function toArray(): array
    {
        $role = $this->roles?->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'shop_id' => $this->shop_id,
            'shop_name' => $this->shop?->name,
            'role_id' => $role?->id,
            'role_name' => $role?->name,
        ];
    }
}
