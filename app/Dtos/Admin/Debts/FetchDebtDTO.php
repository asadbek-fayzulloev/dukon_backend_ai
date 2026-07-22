<?php

namespace App\Dtos\Admin\Debts;

use App\Models\Order;
use App\Models\User;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class FetchDebtDTO extends Data
{
    public int $id;
    public int $amount;
    public int $remaining_amount;
    #[LoadRelation]
    public ?User $user;
    #[LoadRelation]
    public ?Order $order;
    public string $status;
    #[Rule('date_format:Y-m-d H:i:s')]
    public ?string $return_date;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user?->name,
            'user_phone' => $this->user?->phone,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,
            'status' => $this->status,
            'return_date' => $this->return_date,
        ];
    }
}
