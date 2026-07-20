<?php

namespace App\Http\Interceptors\Contracts;

abstract class BaseResponseInterceptor implements ResponseInterceptorContract
{
    public static function shouldRun(InterceptorType $response): bool
    {
        return true;
    }
}
