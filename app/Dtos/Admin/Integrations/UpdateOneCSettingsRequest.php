<?php

namespace App\Dtos\Admin\Integrations;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class UpdateOneCSettingsRequest extends Data
{
    #[Max(500)]
    public ?string $one_c_url;

    #[Max(255)]
    public ?string $one_c_username;

    #[Max(255)]
    public ?string $one_c_password;
}
