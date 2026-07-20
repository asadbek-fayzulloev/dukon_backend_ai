<?php

namespace App\Dtos\Orders;

use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveOrderPaymentRequest extends Data
{
    public string $payment_type;
    public float $payed_price;
}