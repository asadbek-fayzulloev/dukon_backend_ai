<?php

namespace App\Dtos\Mobile\Auth;

use Spatie\LaravelData\Data;

class LoginRequest extends Data
{
    public string $username;
    public string $password;
}