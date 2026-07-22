<?php

namespace App\Dtos\Mobile\Orders;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class FetchOrderRequest extends Data
{
    #[Exists('users', 'id')]
    public ?int $user_id;
    public ?string $payment_type;
    #[Rule('date_format:Y-m-d H:i:s')]
    public ?string $from_date;
    #[Rule('date_format:Y-m-d H:i:s')]
    public ?string $to_date;
    public ?int $page;
    public ?int $per_page;

}