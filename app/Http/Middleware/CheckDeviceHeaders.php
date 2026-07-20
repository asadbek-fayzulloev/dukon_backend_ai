<?php

namespace App\Http\Middleware;

use App\Enums\ResponseCodes\MainRespCode;
use App\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;

class CheckDeviceHeaders
{
    private array $forcedHeaders = [
        UserDevice::X_APP_TYPE, UserDevice::X_DEVICE_TYPE, UserDevice::X_DEVICE_MODEL, UserDevice::X_DEVICE_UID,
        UserDevice::X_APP_VERSION,
        UserDevice::X_APP_BUILD, UserDevice::X_APP_LANG
    ];

    private array $allowedRoutes = [
        'api/v1/ping',
        'api/v1/auth/exist',
        'api/v1/auth/signup',
        'api/v1/auth/logout',
        'api/v1/auth/refresh',
        'api/v1/auth/me',
        'api/v1/otp/send-code',
        'api/v1/landing/inquiries',
        'api/v1/auth/refresh-qr'
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($this->authorize($request)) {
            $this->checkHeadersExistence($request);
            $this->checkSetLang($request);
            $this->checkDeviceType($request);

            device()::syncDevice();
            app()->setLocale($request->header(UserDevice::X_APP_LANG));

            if (in_array($request->path(), $this->allowedRoutes)) {
                return $next($request);
            }

            $this->checkOrganization($request);
        }

        return $next($request);
    }

    public function authorize(Request $request): bool
    {
        return (bool)config('app.enable_checking_headers', true);
    }

    private function checkHeadersExistence(Request $request): void
    {
        $filtered = $this->getFilteredHeaders($request);
        foreach ($this->forcedHeaders as $item) {
            error_unless(isset($filtered[$item]), MainRespCode::APP_MISSING_HEADERS);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getFilteredHeaders(Request $request): array
    {
        $filtered = [];
        foreach ($this->forcedHeaders as $forcedHeader) {
            $filtered[$forcedHeader] = $request->header($forcedHeader);
        }

        return array_filter($filtered);
    }

    private function checkSetLang(Request $request): void
    {
        error_unless(in_array(UserDevice::fromHeader()->lang, array_keys(config('app.languages', []))),
            MainRespCode::APP_WRONG_LANGUAGE);
    }

    private function checkDeviceType(Request $request): void
    {
        error_unless(in_array(UserDevice::fromHeader()->type, UserDevice::TYPES),
            MainRespCode::APP_INVALID_DEVICE_TYPE);
    }

    private function checkOrganization(Request $request): void
    {
        $organization = $request->header(UserDevice::X_APP_ORGANIZATION);
        error_if($organization === null, MainRespCode::APP_INVALID_ORGANIZATION);
    }
}
