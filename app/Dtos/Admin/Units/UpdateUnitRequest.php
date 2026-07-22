<?php

namespace App\Dtos\Admin\Units;

use Spatie\LaravelData\Data;

class UpdateUnitRequest extends Data
{
    public string $name;
}