<?php

namespace App\Http\Interceptors\Response;

use App\Http\Interceptors\Contracts\BaseResponseInterceptor;
use App\Http\Interceptors\Contracts\InterceptorType;

class ServerInfo extends BaseResponseInterceptor
{
    public static function shouldRun(InterceptorType $response): bool
    {
        return ! app()->isProduction();
    }

    public function intercept(InterceptorType $response): InterceptorType
    {
        return $response->setMeta([
            'server' => [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'datetime' => now()->toDateTimeString(),
                'timezone' => config('app.timezone'),
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
            ],
        ]);
    }
}
