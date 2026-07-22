<?php

namespace App\Dtos\Mobile\Debts;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateDebtRequest extends Data
{
    #[Rule('date_format:Y-m-d H:i:s')]
    public string $return_date;
}
