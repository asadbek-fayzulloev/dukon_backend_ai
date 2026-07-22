<?php

namespace App\Dtos\Admin\Auth;

use Spatie\LaravelData\Data;

class LoginRequest extends Data
{
    public string $username;
    public string $password;
}