<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait EnumFromRequestTrait
{
    /**
     * @throw \Throwable
     */
    public static function request(string|Request $request, string $inputName = ''): static
    {
        return static::from(is_string($request) ? input($request) : $request->input($inputName));
    }

    public static function tryRequest(string|Request $request, string $inputName = ''): ?static
    {
        return static::tryFrom(is_string($request) ? input($request) : $request->input($inputName));
    }
}
