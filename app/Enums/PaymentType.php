<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'cash';
    case CARD = 'card';

    public static function all()
    {
        return [
            self::CASH->value,
            self::CARD->value,
        ];
    }
}
