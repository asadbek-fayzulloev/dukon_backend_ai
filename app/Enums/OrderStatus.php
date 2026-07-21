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

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Yakunlangan',
            self::DEBT => 'Qarz',
        };
    }
}
