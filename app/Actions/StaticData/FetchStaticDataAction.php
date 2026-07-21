<?php

namespace App\Actions\StaticData;

use App\Enums\DebtStatus;
use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Enums\PaymentType;
use BackedEnum;

class FetchStaticDataAction
{
    public function handle(): array
    {
        return [
            'payment_types' => $this->options(PaymentType::cases()),
            'discount_types' => $this->options(DiscountType::cases()),
            'order_statuses' => $this->options(OrderStatus::cases()),
            'debt_statuses' => $this->options(DebtStatus::cases()),
        ];
    }

    /**
     * @param array<int, BackedEnum> $cases
     */
    private function options(array $cases): array
    {
        return array_map(fn (BackedEnum $case): array => [
            'value' => $case->value,
            'label' => $case->label(),
        ], $cases);
    }
}
