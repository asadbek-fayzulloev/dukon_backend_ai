<?php

namespace App\Dtos\Users;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveUserRequest extends Data
{
    #[Min(3), Max(100)]
    public ?string $name;
    #[Unique('users', 'phone')]
    public ?string $phone;
}
