<?php

namespace App\Dtos\Admin\Companies;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateCompanyRequest extends Data
{
    #[Min(2), Max(255)]
    public string $name;
}
