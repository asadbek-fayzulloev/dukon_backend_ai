<?php

namespace App\Dtos\Debts;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class PayDebtRequest extends Data
{
    #[Rule(['required', 'integer', 'min:1'])]
    public int $amount;

    #[Rule('in:cash,card')]
    public string $payment_type;

    #[Rule(['nullable', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'])]
    public ?string $paid_at;
}
