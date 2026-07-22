<?php

namespace App\Dtos\Admin\Orders;

use App\Models\Admin;
use App\Models\User;
use DateTime;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class FetchOrdersDTO extends Data
{
    public int $id;
    #[LoadRelation]
    public Admin $seller;
    #[LoadRelation]
    public ?User $user;
    public int $order_total_price;
    #[Rule('date_format:Y-m-d H:i:s')]
    public DateTime $created_at;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->order_total_price,
            'seller_name' => $this->seller->name,
            'user_name' => $this->user?->name,
            'created_at' => $this->created_at,
        ];
    }
}
