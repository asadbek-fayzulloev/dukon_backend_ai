<?php

namespace App\Exceptions;

use Exception;

class ErrorResponse extends Exception
{
    private int $statusCode = 200;

    public function getStatusCode(): int
    {
        $code = $this->getCode();

        return $code >= 400 && $code < 600 ? $code : $this->statusCode;
    }
}
