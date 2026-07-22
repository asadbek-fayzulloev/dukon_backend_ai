<?php

namespace App\Actions\Admin\Auth;

use App\Dtos\Admin\Auth\MeDto;

class MeAction
{
    public function handle(): array
    {
        return [
            'user' => MeDto::from(user())->toArray()
        ];
    }
}