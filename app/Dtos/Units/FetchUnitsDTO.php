<?php

namespace App\Dtos\Units;

use Spatie\LaravelData\Data;

class FetchUnitsDTO extends Data
{
    public int $id;
    public string $name;
}