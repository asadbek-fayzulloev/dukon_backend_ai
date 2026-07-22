<?php

namespace App\Dtos\Mobile\Orders\Save;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class UserRequest extends Data
{
    #[Rule(['required', 'string', 'max:32'])]
    public string $phone;
    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $name;
}
