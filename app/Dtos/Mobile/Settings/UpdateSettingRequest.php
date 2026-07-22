<?php

namespace App\Dtos\Mobile\Settings;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateSettingRequest extends Data
{
    #[Min(3), Max(255), Unique('settings', 'key')]
    public string $key;
    public ?string $value;
    public ?UploadedFile $file;
}
