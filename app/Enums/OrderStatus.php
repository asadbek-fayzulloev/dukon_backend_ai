<?php

namespace App\Enums;

enum OrderStatus: string
{
    case COMPLETED = 'completed';
    case DEBT = 'debt';

    public static function all()
    {
        return [
            self::COMPLETED->value,
            self::DEBT->value,
        ];
    }
}
