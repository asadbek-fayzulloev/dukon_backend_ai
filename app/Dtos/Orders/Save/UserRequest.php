<?php

namespace App\Dtos\Orders\Save;

use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UserRequest extends Data
{
    #[Unique('users', 'phone')]
    public string $phone;
    public ?string $name;
}