<?php

namespace App\Dtos\Mobile\Orders;

use Spatie\LaravelData\Data;

class FetchOrderPaymentsDTO extends Data
{
    public string $payment_type;
    public int $payed_price;

    public function toArray(): array
    {
        return [
            'payment_type' => $this->payment_type,
            'payed_price' => $this->payed_price,
        ];
    }
}
