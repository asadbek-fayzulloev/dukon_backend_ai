<?php

namespace App\Http\Interceptors\Contracts;

interface InterceptorType
{
    public function getData();

    public function getOriginal();

    public function setData($response): InterceptorType;

    public function getMeta(): array;

    public function setMeta(array $meta): InterceptorType;

    public function wrap(string $wrapper): InterceptorType;

    public function is(string $class): bool;

    public function getDataType(): ?string;

    public static function make($response): InterceptorType;

    public function transform(): InterceptorType;

    public function returnOriginal(): InterceptorType;

    public function getOutput();
}
