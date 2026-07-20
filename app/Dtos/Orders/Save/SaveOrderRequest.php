<?php

namespace App\Dtos\Orders\Save;

use App\Dtos\Orders\SaveOrderItemRequest;
use App\Dtos\Orders\SaveOrderPaymentRequest;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class SaveOrderRequest extends Data
{
    #[DataCollectionOf(SaveOrderItemRequest::class)]
    public DataCollection $items;
    #[DataCollectionOf(SaveOrderPaymentRequest::class)]
    public DataCollection $payments;
    #[Exists('users', 'id')]
    public ?int $user_id;
    public ?UserRequest $user;
    #[Rule(['date_format:Y-m-d H:i:s','after:today'])] 
    public ?string $debt_return_date;

}