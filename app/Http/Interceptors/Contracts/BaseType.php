<?php

namespace App\Http\Interceptors\Contracts;

abstract class BaseType implements InterceptorType
{
    private static mixed $original_response;

    private static array $meta = [];

    private static bool $as_original = false;

    private static ?string $wrap = null;

    private mixed $response = '';

    final public function __construct($response)
    {
        self::$original_response = $response;
        $this->response = $response;
    }

    public static function make($response): InterceptorType
    {
        return (new static($response))->transform();
    }

    public function transform(): InterceptorType
    {
        return $this;
    }

    public function getOriginal()
    {
        return self::$original_response;
    }

    public function is(string ...$classes): bool
    {
        foreach ($classes as $class) {
            if ($this->response instanceof $class) {
                return true;
            }
        }

        return false;
    }

    public function returnOriginal(): InterceptorType
    {
        self::$as_original = true;

        return $this;
    }

    public function needOriginal(): bool
    {
        return self::$as_original;
    }

    public function getDataType(): ?string
    {
        return rescue(fn () => get_class($this->response)) ?? gettype($this->response) ?? null;
    }

    public function getMeta(): array
    {
        return self::$meta;
    }

    public function setData($response): InterceptorType
    {
        $this->response = $response;

        return $this;
    }

    public function setMeta(array $meta): InterceptorType
    {
        self::$meta = array_merge_recursive(self::$meta, $meta);

        return $this;
    }

    public function getOutput()
    {
        $data = $this->getData();
        if (self::$wrap) {
            return [
                self::$wrap => $data,
            ];
        }

        return $data;
    }

    public function getData()
    {
        return $this->response;
    }

    public function wrap(string $wrapper): InterceptorType
    {
        if (! empty($wrapper)) {
            self::$wrap = $wrapper;
        }

        return $this;
    }
}
