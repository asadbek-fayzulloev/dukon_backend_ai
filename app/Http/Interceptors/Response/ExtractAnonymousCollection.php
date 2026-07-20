<?php

namespace App\Http\Interceptors\Response;

use App\Http\Interceptors\Contracts\BaseResponseInterceptor;
use App\Http\Interceptors\Contracts\InterceptorType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExtractAnonymousCollection extends BaseResponseInterceptor
{
    public static function shouldRun(InterceptorType $response): bool
    {
        return $response->is(AnonymousResourceCollection::class);
    }

    public function intercept(InterceptorType $response): InterceptorType
    {
        return $response->setData($response->getData()->resource);
    }
}
