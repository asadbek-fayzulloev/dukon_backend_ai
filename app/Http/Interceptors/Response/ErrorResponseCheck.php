<?php

namespace App\Http\Interceptors\Response;

use App\Contracts\ResponseCodeContract;
use App\Http\Interceptors\Contracts\BaseResponseInterceptor;
use App\Http\Interceptors\Contracts\InterceptorType;

class ErrorResponseCheck extends BaseResponseInterceptor
{
    public function intercept(InterceptorType $response): InterceptorType
    {
        error_response($response->getData());
    }

    public static function shouldRun(InterceptorType $response): bool
    {
        return $response->is(ResponseCodeContract::class);
    }
}
