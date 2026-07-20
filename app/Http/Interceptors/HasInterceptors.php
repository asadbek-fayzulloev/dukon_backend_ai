<?php

namespace App\Http\Interceptors;

use App\Http\Interceptors\Contracts\InterceptorType;
use App\Http\Interceptors\Contracts\ResponseInterceptorContract;
use App\Http\Interceptors\Types\GenericType;
use Closure;
use Throwable;

/**
 * @property array|null $interceptors protected
 */
trait HasInterceptors
{
    public const SUCCESS_KEY = 'success';

    public const DATA_KEY = 'data';

    public const ERROR_KEY = 'error';

    public const META_KEY = 'meta';

    public function callAction($method, $parameters)
    {
        return $this->run(parent::callAction($method, $parameters));
    }

    public function run($response)
    {
        $typed_response = $this->runInterceptors(new GenericType($response));

        return $this->prepareToOutput($typed_response);
    }

    private function runInterceptors(InterceptorType $response): InterceptorType
    {
        if (! config('interceptor.enabled')) {
            return $response->returnOriginal();
        }

        if (is_string($response->getOriginal())) {
            return $response->setData(['message' => $response->getOriginal()]);
        }

        foreach (config('interceptor.ignored_types') as $item) {
            if ($response->is($item)) {
                return $response->returnOriginal();
            }
        }

        $filtered_interceptors = $this->getFilteredInterceptors($this->getInterceptors());

        foreach ($filtered_interceptors as $interceptor) {
            /** @var ResponseInterceptorContract $interceptor */
            if ($interceptor::shouldRun($response)) {
                try {
                    $response = app($interceptor)->intercept($response);
                } catch (Throwable $th) {
                    if (! config('interceptor.silent', false)) {
                        throw $th;
                    }
                }
            }
        }

        return $response;
    }

    private function getFilteredInterceptors(array $interceptors): array
    {
        $filtered = [];

        foreach ($interceptors as $name => $interceptor) {
            if ($interceptor instanceof Closure) {
                if ($interceptor()) {
                    $interceptor = $name;
                } else {
                    continue;
                }
            }

            if ($this->isValidInterceptor($interceptor)) {
                $filtered[] = $interceptor;

                continue;
            }

            $group = config("interceptor.groups.$interceptor");
            if ($group) {
                $group_interceptors = array_filter($group, [$this, 'isValidInterceptor']);
                $filtered = array_merge($filtered, $group_interceptors);
            }
        }

        return $filtered;
    }

    private function isValidInterceptor($interceptor): bool
    {
        return is_string($interceptor) && class_exists($interceptor) && is_subclass_of($interceptor, ResponseInterceptorContract::class);
    }

    private function getInterceptors()
    {
        if (property_exists($this, 'interceptors') && is_array($this->interceptors)) {
            return $this->interceptors;
        }

        $interceptors = config('interceptor.interceptors');

        return is_array($interceptors) ? $interceptors : [];
    }

    private function prepareToOutput(InterceptorType $response)
    {
        if ($response->needOriginal()) {
            return $response->getOriginal();
        }

        $data = [
            self::SUCCESS_KEY => true,
            self::DATA_KEY => $response->getOutput(),
            self::ERROR_KEY => null,
        ];

        $meta = $response->getMeta();
        if (! empty($meta)) {
            $data[self::META_KEY] = $meta;
        }

        return $data;
    }
}
