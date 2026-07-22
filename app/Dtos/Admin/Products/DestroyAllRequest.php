<?php

namespace App\Dtos\Admin\Products;

use Spatie\LaravelData\Data;

class DestroyAllRequest extends Data
{
    public array $ids;
}