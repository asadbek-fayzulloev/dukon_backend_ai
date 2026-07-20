<?php

namespace App\Dtos\Debts;

use App\Models\Order;
use App\Models\User;
use DateTime;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class GetDebtDTO extends Data
{
    public int $id;
    public ?float $amount;
    public ?DateTime $return_date;
    public ?string $status;
    public ?bool $is_notified;
    #[LoadRelation]
    public ?Order $order;
    #[LoadRelation]
    public ?User $user;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'is_notified' => $this->is_notified,
            'user_name' => $this->user->name,
            'user_id' => $this->user->id,
        ];
    }
}