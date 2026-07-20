<?php

namespace App\Http\Interceptors\Response;

use App\Http\Interceptors\Contracts\BaseResponseInterceptor;
use App\Http\Interceptors\Contracts\InterceptorType;
use App\Http\Interceptors\Types\Pageable;
use Illuminate\Contracts\Pagination\Paginator;

class MakePageable extends BaseResponseInterceptor
{
    public static function shouldRun(InterceptorType $response): bool
    {
        return $response->is(Paginator::class);
    }

    public function intercept(InterceptorType $response): InterceptorType
    {
        return Pageable::make($response->getData());
    }
}
