<?php

namespace App\Enums;

enum OrderStatus: string
{
    case CREATED = 'click';
    case NOTIFIED = 'payme';
    case PAYED = 'naqd';

    public static function all()
    {
        return [
            self::CREATED->value,
            self::NOTIFIED->value,
            self::PAYED->value,
        ];
    }
}
