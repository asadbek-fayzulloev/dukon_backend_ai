<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public static function all()
    {
        return [
            self::PERCENTAGE->value,
            self::FIXED->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Foiz',
            self::FIXED => 'Summa',
        };
    }
}
