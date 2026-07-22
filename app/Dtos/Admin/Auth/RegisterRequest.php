<?php

namespace App\Dtos\Admin\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RegisterRequest extends Data
{
    #[Min(2), Max(255)]
    public string $company_name;

    #[Min(2), Max(255)]
    public string $name;

    #[Email, Unique('admins', 'email')]
    public string $email;

    #[Min(6)]
    public string $password;
}
