<?php

namespace App\Dtos\Mobile\Products;

use Spatie\LaravelData\Data;

class DestroyAllRequest extends Data
{
    public array $ids;
}