<?php

namespace App\Dtos\Users;

use Spatie\LaravelData\Data;

class GetUserDTO extends Data
{
    public int $id;
    public string $name;
    public string $phone;

}