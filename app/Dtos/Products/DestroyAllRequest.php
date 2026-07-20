<?php

namespace App\Dtos\Products;

use Spatie\LaravelData\Data;

class DestroyAllRequest extends Data
{
    public array $ids;
}