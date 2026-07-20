<?php

namespace App\Enums;

enum OtpType: string
{
    case SMS = 'sms';
    case EMAIL = 'email';

    case VERIFICATION_CACHE_KEY = 'sms_verify_code';
    case IS_EXIST_PHONE_CACHE_KEY = 'sms_exist_phone_code';
    public static function all(): array
    {
        return [
            self::SMS->value,
            self::EMAIL->value,
            self::VERIFICATION_CACHE_KEY->value,
            self::IS_EXIST_PHONE_CACHE_KEY->value,

        ];
    }
}
