<?php

namespace App\Enums\ResponseCodes;

use App\Contracts\ResponseCodeContract;

enum OtpRespCode: int implements ResponseCodeContract
{
    case OTP_OR_PHONE_NOT_FOUND = 2000;
    case INCORRECT_CODE         = 2001;
    case MESSAGE_NOT_FOUND      = 2002;
    case EXPIRED                = 2003;

    case WRONG_REASON_FOR_USER        = 2004;
    case NOT_FOUND_OR_SESSION_EXPIRED = 2005;
    case PHONE_NUMBER_REQUIRED        = 2006;

    case OTP_REASON_NOT_FOUND    = 2007;
    case OTP_SMS_PROVIDER_FAILED = 2008;

    case USER_NOT_FOUND = 2009;

    public function message(): string
    {
        return match ($this) {
            self::OTP_OR_PHONE_NOT_FOUND       => __('app.otp.otp_or_phone_not_found'),
            self::INCORRECT_CODE               => __('app.otp.incorrect'),
            self::MESSAGE_NOT_FOUND            => __('app.otp.not_found'),
            self::EXPIRED                      => __('app.otp.expired'),
            self::WRONG_REASON_FOR_USER        => __('app.otp.reason_not_for_users'),
            self::NOT_FOUND_OR_SESSION_EXPIRED => __('app.otp.not_found_or_session_expired'),
            self::PHONE_NUMBER_REQUIRED        => __('app.otp.phone_number_required'),
            self::OTP_REASON_NOT_FOUND         => __('app.otp.otp_reason_not_found'),
            self::OTP_SMS_PROVIDER_FAILED      => __('app.otp.sms_provider_failed'),
            self::USER_NOT_FOUND               => __('app.otp.user_not_found'),
            default                            => 'Error'
        };
    }

    public function statusCode(): int
    {
        return 200;
    }
}
