<?php

namespace App\Dtos\Statistics;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class PaymentStatRequest extends Data
{
    #[Rule('date_format:Y-m-d')]
    public ?string $from_date;
    #[Rule('date_format:Y-m-d')]
    public ?string $to_date;
}
