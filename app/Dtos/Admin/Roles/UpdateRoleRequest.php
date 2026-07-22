<?php

namespace App\Dtos\Admin\Roles;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateRoleRequest extends Data
{
    #[Min(2), Max(255)]
    public string $name;

    /** @var int[] */
    #[Exists('permissions', 'id')]
    public array $permission_ids = [];
}
