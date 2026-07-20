<?php

namespace App\Enums;

enum PaymentType: string
{
    case CLICK = 'click';
    case PAYME = 'payme';
    case CASH = 'naqd';
    case TRANSFER = "pul ko'chirish";
    case VISA = 'visa';

    public static function all()
    {
        return [
            self::CLICK->value,
            self::PAYME->value,
            self::CASH->value,
            self::TRANSFER->value,
            self::VISA->value,
        ];
    }
}
