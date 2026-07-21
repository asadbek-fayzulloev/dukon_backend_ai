<?php

namespace App\Enums;

enum DebtStatus: string
{
    case OPEN = 'open';
    case PAID = 'paid';

    public static function all()
    {
        return [
            self::OPEN->value,
            self::PAID->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Ochiq',
            self::PAID => 'To‘langan',
        };
    }
}
