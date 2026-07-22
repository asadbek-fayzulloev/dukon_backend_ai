<?php

namespace App\Dtos\Mobile\Roles;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SaveRoleRequest extends Data
{
    #[Min(2), Max(255), Unique('roles', 'name')]
    public string $name;

    /** @var int[] */
    #[Exists('permissions', 'id')]
    public array $permission_ids = [];
}
