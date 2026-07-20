<?php

namespace App\Http\Interceptors\Contracts;

interface ResponseInterceptorContract
{
    public static function shouldRun(InterceptorType $response): bool;

    public function intercept(InterceptorType $response): InterceptorType;
}
