<?php

namespace App\Http\Interceptors\Response;

use App\Http\Interceptors\Contracts\BaseResponseInterceptor;
use App\Http\Interceptors\Contracts\InterceptorType;

class DetectKeyed extends BaseResponseInterceptor
{
    public static function shouldRun(InterceptorType $response): bool
    {
        $data = $response->getData();

        return (is_array($data) && count($data) === 1) || is_string($data);
    }

    public function intercept(InterceptorType $response): InterceptorType
    {
        if (is_string($response->getData())) {
            return $response->wrap('message');
        }

        foreach ($response->getData() as $key => $value) {
            if (is_string($key)) {
                return $response->wrap($key)->setData($value);
            }
        }

        return $response;
    }
}
