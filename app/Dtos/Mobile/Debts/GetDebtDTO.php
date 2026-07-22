<?php

namespace App\Dtos\Mobile\Debts;

use App\Models\Order;
use App\Models\User;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class GetDebtDTO extends Data
{
    public int $id;
    public ?int $amount;
    public int $remaining_amount;
    public ?DateTime $return_date;
    public ?string $status;
    public ?bool $is_notified;
    #[LoadRelation]
    public ?Order $order;
    #[LoadRelation]
    public ?User $user;
    #[LoadRelation]
    public ?Collection $payments;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'is_notified' => $this->is_notified,
            'user_name' => $this->user->name,
            'user_id' => $this->user->id,
            'payments' => $this->payments?->map(fn ($payment): array => [
                'id' => $payment->id,
                'payment_type' => $payment->payment_type,
                'amount' => (int) $payment->amount,
                'paid_at' => $payment->paid_at,
            ])->values()->all() ?? [],
        ];
    }
}
