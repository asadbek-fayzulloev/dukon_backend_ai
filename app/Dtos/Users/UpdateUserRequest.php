<?php

namespace App\Dtos\Users;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateUserRequest extends Data
{
    #[Min(3), Max(100)]
    public ?string $name;
    #[Min(12), Max(12)]
    public ?string $phone;
}