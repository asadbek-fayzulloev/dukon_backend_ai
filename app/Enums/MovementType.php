<?php

namespace App\Enums;

enum MovementType: string
{
    case BUY = 'buy';
    case SOLD = 'sold';
    case TRANSFER = 'transfer';

    public static function all(): array
    {
        return [
            self::BUY->value,
            self::SOLD->value,
            self::TRANSFER->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::BUY => 'Kirim',
            self::SOLD => 'Sotuv',
            self::TRANSFER => "Ko'chirish",
        };
    }
}
