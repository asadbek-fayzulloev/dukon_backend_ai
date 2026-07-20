<?php

namespace App\Enums\ResponseCodes;

use App\Contracts\ResponseCodeContract;

enum AuthRespCode: int implements ResponseCodeContract
{
    case AUTHORIZATION = 3000;
    case SIGNUP_FAILED = 3001;
    case WRONG_PASSWORD = 3002;
    case USER_NOT_FOUND = 3003;
    case SECRET_WORD_INCORRECT = 3004;
    case PASSWORD_ALREADY_USED = 3005;
    case USER_ALREADY_EXISTS = 3006;
    case FORBIDDEN = 3007;
    case REFRESH_TOKEN_EXPIRED = 3008; // DON'T TOUCH THIS LINE. BECAUSE IT IS USED IN "Mobile Apps".
    case WRONG_JWT_TOKEN_TYPE = 3009; // DON'T TOUCH THIS LINE. BECAUSE IT IS USED IN "Mobile Apps".

    public function message(): string
    {
        return match ($this) {
            self::AUTHORIZATION => __('app.authorization_failed'),
            self::SIGNUP_FAILED => __('app.signup_failed'),
            self::WRONG_PASSWORD => __('app.wrong_password'),
            self::USER_NOT_FOUND => __('app.user_not_found'),
            self::SECRET_WORD_INCORRECT => __('user.secret_word_incorrect'),
            self::PASSWORD_ALREADY_USED => __('user.password_already_used'),
            self::USER_ALREADY_EXISTS => __('user.user_already_exists'),
            self::FORBIDDEN => __('user.forbidden'),
            self::REFRESH_TOKEN_EXPIRED => __('user.refresh_token_expired'),
            default => 'Authorization error'
        };
    }

    public function statusCode(): int
    {
        return match ($this) {
            self::REFRESH_TOKEN_EXPIRED, self::WRONG_JWT_TOKEN_TYPE => 401,
            default => 200
        };
    }
}
