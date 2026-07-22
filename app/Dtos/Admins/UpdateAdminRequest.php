<?php

namespace App\Dtos\Admins;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateAdminRequest extends Data
{
    #[Min(2), Max(255)]
    public string $name;

    #[Email]
    public string $email;

    #[Min(6)]
    public ?string $password;

    #[Exists('shops', 'id')]
    public ?int $shop_id;
}
