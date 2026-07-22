<?php

namespace App\Dtos\Mobile\Orders;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class SaveOrderPaymentRequest extends Data
{
    #[Rule('in:cash,card,transfer')]
    public string $payment_type;
    #[Rule(['integer', 'min:1'])]
    public int $payed_price;
}
