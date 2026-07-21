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
    #[Rule(['required', 'array', 'min:1'])]
    public DataCollection $items;
    #[DataCollectionOf(SaveOrderPaymentRequest::class)]
    #[Rule(['present', 'array', 'max:3'])]
    public DataCollection $payments;
    #[Rule(['nullable', 'uuid'])]
    public ?string $uuid;
    #[Exists('warehouses', 'id')]
    public int $warehouse_id;
    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $device_id;
    #[Rule(['nullable', 'in:percentage,fixed'])]
    public ?string $discount_type;
    #[Rule(['nullable', 'numeric', 'min:0'])]
    public ?float $discount_value;
    #[Exists('users', 'id')]
    public ?int $user_id;
    public ?UserRequest $user;
    #[Rule(['nullable', 'date_format:Y-m-d H:i:s', 'after:now'])]
    public ?string $debt_return_date;
    #[Rule(['nullable', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'])]
    public ?string $sold_at;
}
