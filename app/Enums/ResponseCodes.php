<?php

namespace App\Enums;

enum ResponseCodes: int
{
    case OK = 0;
    case UNKNOWN = -999;
    case APP_MISSING_HEADERS = -1000;
    case APP_WRONG_LANGUAGE = -1001;
    case APP_INVALID_DEVICE_TYPE = -1002;
    case APP_VERSION_OUTDATED = -1003;
    case APP_INVALID_DEVICE_MODEL = -1004;
    case APP_INVALID_HEADERS = -1005;

    case OTP_INCORRECT = 1100;
    case OTP_EXPIRED = 1101;
    case SMS_SEND_ERROR = 1102;

    case USER_INACTIVE = 1200;
    case ALREADY_REGISTERED = 1201;
    case LOGIN_FAILED = 1202;

    case TRIP_NOT_FOUND = 1300;
    case TRIP_ALREADY_CREATED = 1301;
    case TRIP_VIEW_NOT_ALLOWED = 1302;

    case TRIP_DONE = 1303;

    case USER_NOT_FOUND = 1304;

    case ACCOUNT_DELETED_LIMIT_EXCEEDED = 1305;

    case SOMETHING_WENT_WRONG = 1306;

    case INSUFFICIENT_BALANCE = 1307;

    case INVALID_DISTRICT_ID = 1308;

    case CHAT_SESSION_NOT_FOUND = 1309;

    case TRIP_NOT_ENOUGH_BALANCE = 1310;

    public function message(): string
    {
        return match ($this) {
            self::OTP_INCORRECT => __('message.otp_incorrect'),
            self::OTP_EXPIRED => __('message.otp_expired'),
            self::SMS_SEND_ERROR => __('message.sms_send_error'),

            self::APP_MISSING_HEADERS => __('message.app_missing_headers'),
            self::APP_WRONG_LANGUAGE => __('message.app_wrong_language'),
            self::APP_INVALID_DEVICE_TYPE => __('message.app_invalid_device_type'),
            self::APP_VERSION_OUTDATED => __('message.app_version_outdated'),
            self::APP_INVALID_DEVICE_MODEL => __('message.app_invalid_device_model'),
            self::APP_INVALID_HEADERS => __('message.app_invalid_headers'),

            self::USER_INACTIVE => __('message.user_inactive'),
            self::USER_NOT_FOUND => __('message.user_not_found'),
            self::ALREADY_REGISTERED => __('message.already_registered'),
            self::LOGIN_FAILED => __('message.login_failed'),

            self::TRIP_NOT_FOUND => __('message.trip_not_found'),
            self::TRIP_ALREADY_CREATED => __('message.trip_already_created'),
            self::TRIP_VIEW_NOT_ALLOWED => __('message.trip_view_not_allowed'),
            self::TRIP_DONE => __('message.trip_done'),
            self::SOMETHING_WENT_WRONG => __('message.something_went_wrong'),
            self::ACCOUNT_DELETED_LIMIT_EXCEEDED => __('message.account_deleted_limit_exceeded'),
            self::INSUFFICIENT_BALANCE => __('message.insufficient_balance'),
            self::INVALID_DISTRICT_ID => __('message.invalid_district_id'),
            self::CHAT_SESSION_NOT_FOUND => __('message.chat_session_not_found'),
            self::TRIP_NOT_ENOUGH_BALANCE => __('message.trip_not_enough_balance'),

            default => __('message.something_went_wrong'),
        };
    }
}
