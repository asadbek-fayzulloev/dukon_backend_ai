<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case TRANSFER = 'transfer';

    public static function all()
    {
        return [
            self::CASH->value,
            self::CARD->value,
            self::TRANSFER->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Naqd',
            self::CARD => 'Karta',
            self::TRANSFER => 'Pul o‘tkazmasi',
        };
    }
}
