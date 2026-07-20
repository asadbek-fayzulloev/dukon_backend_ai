<?php

namespace App\Contracts;

interface ResponseCodeContract
{
    public function message(): string;
    public function statusCode(): int;
}
