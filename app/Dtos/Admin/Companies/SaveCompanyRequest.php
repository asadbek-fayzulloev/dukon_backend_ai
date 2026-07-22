<?php

namespace App\Dtos\Admin\Companies;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveCompanyRequest extends Data
{
    #[Min(2), Max(255), Unique('companies', 'name')]
    public string $name;
}
